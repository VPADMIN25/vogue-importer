<?php
// index2new.php (Végleges Verzió V4 - Variant SKU alapú csoportosítás)

ini_set('max_execution_time', 0);
set_time_limit(0);

echo "<h2>FUTÁS INDUL: 2. Lépés - ÚJ TERMÉKEK LÉTREHOZÁSA</h2>";

// --- 1. ADATBÁZIS KAPCSOLAT ---
$host = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASS');
$dbname = getenv('DB_NAME');
$port = (int)getenv('DB_PORT');
$sslmode = getenv('DB_SSLMODE');
$conn = mysqli_init();
if ($sslmode === 'require') { mysqli_options($conn, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false); }
if (!mysqli_real_connect($conn, $host, $username, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL)) {
    die("Connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");
echo "Adatbázis-kapcsolat sikeres.<br>";

// --- 2. HELPERS ÉS SHOPIFY KREDENCIÁLISOK ---
require_once("helpers/shopifyGraphQL.php");
require_once("helpers/general.php");

$shopurl = getenv('SHOPIFY_SHOP_URL');
$token = getenv('SHOPIFY_API_TOKEN');
if (empty($shopurl) || empty($token)) {
    die("Hiányzó Környezeti Változók: SHOPIFY_SHOP_URL vagy SHOPIFY_API_TOKEN.");
}
echo "Shopify kredenciálisok betöltve ($shopurl).<br>";

// --- 3. RAKTÁRHELYEK ---
$location_name_1 = "Italy Vogue Premiere Warehouse 1";
$location_name_2 = "Italy Vogue Premiere Warehouse 2";
$location_gid_1 = getShopifyLocationGid($token, $shopurl, $location_name_1);
$location_gid_2 = getShopifyLocationGid($token, $shopurl, $location_name_2);
if (empty($location_gid_1) || empty($location_gid_2)) {
    die("Kritikus hiba: Raktárhely nem található!");
}
echo "Raktárhely GID-jei lekérdezve.<br>";

// --- 4. ÚJ TERMÉKEK LEKÉRDEZÉSE ---
$group_sql = "SELECT DISTINCT variant_sku FROM shopifyproducts WHERE needs_update = 2 LIMIT 50";
$group_result = $conn->query($group_sql);
if (!$group_result) die("Hiba a lekérdezés során: " . $conn->error);
if ($group_result->num_rows == 0) {
    echo "Nincs új termék létrehozásra.<br>";
    $conn->close();
    exit;
}
echo "Feldolgozás alatt: <b>{$group_result->num_rows}</b> termékcsoport...<br>";

while ($group_row = $group_result->fetch_assoc()) {
    $variant_sku_group = $group_row['variant_sku'];
    if (empty($variant_sku_group)) continue;

    echo "<hr>Feldolgozás: <b>$variant_sku_group</b><br>";

    $variants_sql = "SELECT * FROM shopifyproducts WHERE variant_sku = ? AND needs_update = 2";
    $stmt = $conn->prepare($variants_sql);
    $stmt->bind_param("s", $variant_sku_group);
    $stmt->execute();
    $variants_result = $stmt->get_result();
    if ($variants_result->num_rows == 0) { $stmt->close(); continue; }

    $variants_data = [];
    $images_data = [];
    $options_array = [];
    $local_ids_to_update = [];
    $first_row = null;

    while ($variant_row = $variants_result->fetch_assoc()) {
        if (!$first_row) $first_row = $variant_row;
        $local_ids_to_update[] = $variant_row['id'];

        $variant_input = [
            "sku" => $variant_row['generated_sku'],
            "price" => (string)$variant_row['price_huf'],
            "inventoryPolicy" => "DENY",
            "inventoryQuantities" => [
                ["locationId" => $location_gid_1, "availableQuantity" => (int)$variant_row['qty_location_1']],
                ["locationId" => $location_gid_2, "availableQuantity" => (int)$variant_row['qty_location_2']]
            ]
        ];
        if (!empty($variant_row['option1_value'])) $variant_input["option1"] = $variant_row['option1_value'];
        if (!empty($variant_row['option2_value'])) $variant_input["option2"] = $variant_row['option2_value'];
        $variants_data[] = $variant_input;

        if (!empty($variant_row['img_src'])) $images_data[] = ["src" => $variant_row['img_src']];
        if (!empty($variant_row['img_src_2'])) $images_data[] = ["src" => $variant_row['img_src_2']];
        if (!empty($variant_row['img_src_3'])) $images_data[] = ["src" => $variant_row['img_src_3']];

        if (!empty($variant_row['option1_name']) && !in_array($variant_row['option1_name'], $options_array)) {
            $options_array[] = $variant_row['option1_name'];
        }
        if (!empty($variant_row['option2_name']) && !in_array($variant_row['option2_name'], $options_array)) {
            $options_array[] = $variant_row['option2_name'];
        }
    }
    $stmt->close();

    $images_data = array_values(array_unique(array_filter($images_data), SORT_REGULAR));
    $handle_to_use = !empty($first_row['handle']) ? sanitize_handle($first_row['handle']) : sanitize_handle($first_row['variant_sku']);

    $product_input = [
        "title" => $first_row['title'],
        "handle" => $handle_to_use,
        "bodyHtml" => $first_row['body'],
        "vendor" => $first_row['vendor'],
        "productType" => $first_row['type'],
        "tags" => $first_row['tags'],
        "options" => array_values($options_array),
        "variants" => $variants_data,
        "images" => $images_data,
        "status" => "ACTIVE"
    ];

    $variables = ["input" => $product_input];
    echo "Shopify productCreate hívása (Handle: $handle_to_use)...<br>";
    $response = productCreate_graphql($token, $shopurl, $variables);

    if (isset($response['data']['productCreate']['product']['id'])) {
        $product = $response['data']['productCreate']['product'];
        $product_gid = $product['id'];
        $shopify_variants = $product['variants']['nodes'];

        echo "Termék létrehozva. GID: $product_gid<br>";

        foreach ($shopify_variants as $sv) {
            $generated_sku_from_shopify = $sv['sku'];
            if (empty($generated_sku_from_shopify)) continue;

            $variant_gid = $sv['id'];
            $inventory_gid = $sv['inventoryItem']['id'];

            $update_sql = "UPDATE shopifyproducts SET shopifyproductid = ?, shopifyvariantid = ?, shopifyinventoryid = ?, needs_update = 0 WHERE generated_sku = ?";
            $stmt_update = $conn->prepare($update_sql);
            $stmt_update->bind_param("sssss", $product_gid, $variant_gid, $inventory_gid, $generated_sku_from_shopify, $generated_sku_from_shopify);
            if ($stmt_update->execute()) {
                echo "DB frissítve: $generated_sku_from_shopify<br>";
            } else {
                echo "DB hiba: " . $stmt_update->error . "<br>";
            }
            $stmt_update->close();
        }
    } else {
        echo "HIBA a termék létrehozása közben.<br>";
        if (isset($response['errors'])) {
            echo "<pre>" . json_encode($response['errors'], JSON_PRETTY_PRINT) . "</pre>";
        } elseif (isset($response['data']['productCreate']['userErrors'])) {
            echo "<pre>" . json_encode($response['data']['productCreate']['userErrors'], JSON_PRETTY_PRINT) . "</pre>";
        }
    }
}

echo "<h2>Befejezve: 2. Lépés - ÚJ TERMÉKEK LÉTREHOZÁSA</h2>";
$conn->close();

function sanitize_handle($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');
    return $text ?: 'product';
}
?>

