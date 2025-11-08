<?php
// indexnew.php (V8 - DigitalOcean App Platform Kompatibilis)

function sanitize_key($text) {
    return preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($text)));
}

ini_set('max_execution_time', 0);
set_time_limit(0);
ini_set('memory_limit', '1024M');
echo "<pre style='font-family:Consolas;font-size:14px'>";
echo "<h2>FUTÁS INDUL: 1. LÉPÉS – BEOLVASÁS és SZINKRONIZÁLÁS (Generált SKU alapú)</h2>";

// --- 1. ADATBÁZIS KAPCSOLAT (RETRY-VAL) ---
$env = [
    'DB_HOST' => getenv('DB_HOST'),
    'DB_USER' => getenv('DB_USER'),
    'DB_PASS' => getenv('DB_PASS'),
    'DB_NAME' => getenv('DB_NAME'),
    'DB_PORT' => (int)getenv('DB_PORT'),
    'DB_SSLMODE' => getenv('DB_SSLMODE')
];

$conn = null;
$maxRetries = 5;
for ($i = 0; $i < $maxRetries; $i++) {
    $conn = mysqli_init();
    if ($env['DB_SSLMODE'] === 'require') {
        mysqli_options($conn, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);
    }
    if (@mysqli_real_connect($conn, $env['DB_HOST'], $env['DB_USER'], $env['DB_PASS'], $env['DB_NAME'], $env['DB_PORT'], NULL, MYSQLI_CLIENT_SSL)) {
        break;
    }
    echo "Kapcsolódás sikertelen... próbálkozás " . ($i + 1) . "/$maxRetries (5mp várakozás)<br>";
    sleep(5);
}
if (!$conn) die("FATAL: Nem sikerült kapcsolódni a MySQL-hez!");
mysqli_set_charset($conn, "utf8mb4");
echo "Adatbázis-kapcsolat sikeres.<br>";

// --- 2. SHOPIFY KREDENCIÁLISOK ---
require_once("helpers/shopifyGraphQL.php");
$shopurl = getenv('SHOPIFY_SHOP_URL');
$token = getenv('SHOPIFY_API_TOKEN');
if (empty($shopurl) || empty($token)) {
    die("Hiányzó Környezeti Változók: SHOPIFY_SHOP_URL vagy SHOPIFY_API_TOKEN.");
}
echo "Shopify kredenciálisok betöltve ($shopurl).<br>";

// --- 3. RAKTÁRHELYEK (LOCATIONS) ELLENŐRZÉSE ---
$location_name_1 = "Italy Vogue Premiere Warehouse 1";
$location_name_2 = "Italy Vogue Premiere Warehouse 2";
if (empty(getShopifyLocationGid($token, $shopurl, $location_name_1)) || empty(getShopifyLocationGid($token, $shopurl, $location_name_2))) {
    die("Kritikus hiba: A '$location_name_1' vagy '$location_name_2' raktárhely nem található!");
}
echo "Raktárhelyek GID-jei sikeresen ellenőrizve.<br>";

// --- 4. A FUTÁS IDŐBÉLYEGE ---
$run_timestamp = date('Y-m-d H:i:s');
echo "Futás időbélyege: $run_timestamp <br>";

// --- 5. FEED KONFIGURÁCIÓ ---
$feeds_to_process = [
    [
        'url' => 'https://voguepremiere-csv-storage.fra1.digitaloceanspaces.com/stockfirmati_final_feed_huf.csv',
        'location_index' => 1,
        'quantity_column_name' => 'Stockfirmati Raktár Inventory Qty',
        'currency' => 'huf'
    ],
    [
        'url' => 'https://voguepremiere-csv-storage.fra1.digitaloceanspaces.com/peppela_final_feed_huf.csv',
        'location_index' => 2,
        'quantity_column_name' => 'Peppela Inventory Qty',
        'currency' => 'huf'
    ],
];

// --- 6. SQL ELŐKÉSZÍTÉS ---
$stmt_check_db = $conn->prepare("SELECT id, shopifyproductid, needs_update, qty_location_1, qty_location_2 FROM shopifyproducts WHERE generated_sku = ?");
$stmt_update_db = $conn->prepare(
    "UPDATE shopifyproducts SET 
        price_huf = ?, qty_location_1 = ?, qty_location_2 = ?, 
        needs_update = ?, last_seen_in_feed = ?,
        handle = ?, title = ?, body = ?, vendor = ?, type = ?, tags = ?, 
        variant_sku = ?, barcode = ?, grams = ?,
        img_src = ?, img_src_2 = ?, img_src_3 = ?,
        option1_name = ?, option1_value = ?, option2_name = ?, option2_value = ?,
        updated_at = NOW()
     WHERE generated_sku = ?"
);
$stmt_insert_db = $conn->prepare(
    "INSERT INTO shopifyproducts (
        handle, title, body, vendor, type, tags,
        variant_sku, generated_sku, barcode, grams, inventory_tracker,
        img_src, img_src_2, img_src_3,
        option1_name, option1_value, option2_name, option2_value,
        price_huf, qty_location_1, qty_location_2,
        shopifyproductid, shopifyvariantid, shopifyinventoryid,
        needs_update, last_seen_in_feed,
        created_at, updated_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())"
);

// --- 7. FEED FELDOLGOZÁS ---
$total_rows_processed = 0; $total_adopted = 0; $total_created = 0; $total_updated = 0;
foreach ($feeds_to_process as $feed) {
    echo "<hr><h3>Feed feldolgozása: {$feed['url']}</h3>";
    $feedContent = @file_get_contents($feed['url']);
    if ($feedContent === false) {
        echo "Hiba a feed letöltése közben: {$feed['url']}<br>";
        continue;
    }
    $temp = fopen("php://memory", 'r+');
    fwrite($temp, $feedContent);
    rewind($temp);

    $headers = fgetcsv($temp, 0, ",", "\"", "\\");
    $normalizedHeaders = array_map('trim', array_map('strtolower', $headers));

    $map = [
        'handle' => array_search('handle', $normalizedHeaders),
        'title' => array_search('title', $normalizedHeaders),
        'body' => array_search('body (html)', $normalizedHeaders),
        'vendor' => array_search('vendor', $normalizedHeaders),
        'type' => array_search('type', $normalizedHeaders),
        'tags' => array_search('tags', $normalizedHeaders),
        'sku' => array_search('variant sku', $normalizedHeaders),
        'price' => array_search('variant price', $normalizedHeaders),
        'barcode' => array_search('variant barcode', $normalizedHeaders),
        'grams' => array_search('variant grams', $normalizedHeaders),
        'tracker' => array_search('variant inventory tracker', $normalizedHeaders),
        'img1' => array_search('image src', $normalizedHeaders),
        'img2' => array_search('image src 2', $normalizedHeaders),
        'img3' => array_search('image src 3', $normalizedHeaders),
        'opt1_name' => array_search('option1 name', $normalizedHeaders),
        'opt1_val' => array_search('option1 value', $normalizedHeaders),
        'opt2_name' => array_search('option2 name', $normalizedHeaders),
        'opt2_val' => array_search('option2 value', $normalizedHeaders),
        'qty' => array_search(strtolower($feed['quantity_column_name']), $normalizedHeaders),
        'is_changed' => array_search('is changed', $normalizedHeaders),
    ];

    $required = ['handle', 'title', 'vendor', 'sku', 'price', 'qty', 'is_changed'];
    foreach ($required as $key) {
        if ($map[$key] === false) {
            echo "Kritikus oszlop hiányzik: " . ucfirst(str_replace('_', ' ', $key)) . "<br>";
            continue 2;
        }
    }

    while (($data = fgetcsv($temp, 0, ",", "\"", "\\")) !== FALSE) {
        $total_rows_processed++;

        $variantSkuGroup = trim($data[$map['sku']]);
        $option1Val = trim($data[$map['opt1_val']]);
        $option2Val = trim($data[$map['opt2_val']]);

        $generated_sku = $variantSkuGroup;
        if (!empty($option1Val)) $generated_sku .= "-" . sanitize_key($option1Val);
        if (!empty($option2Val)) $generated_sku .= "-" . sanitize_key($option2Val);

        if (empty($variantSkuGroup)) {
            echo "Sor átugorva: Hiányzó Variant SKU.<br>";
            continue;
        }

        $newPrice = (float)$data[$map['price']];
        $newQuantity = (int)$data[$map['qty']];
        $isChanged = (strtolower(trim($data[$map['is_changed']])) === 'true');

        $stmt_check_db->bind_param("s", $generated_sku);
        $stmt_check_db->execute();
        $result = $stmt_check_db->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $needs_update_flag = $row['needs_update'];
            if ($needs_update_flag == 20) $needs_update_flag = 1;
            else if ($isChanged && !in_array($needs_update_flag, [2, 10])) $needs_update_flag = 1;
            else if (!$isChanged && $needs_update_flag == 1) $needs_update_flag = 0;

            $qty1 = ($feed['location_index'] == 1) ? $newQuantity : $row['qty_location_1'];
            $qty2 = ($feed['location_index'] == 2) ? $newQuantity : $row['qty_location_2'];

            $stmt_update_db->bind_param("diiissssssisssssssssss",
                $newPrice, $qty1, $qty2, $needs_update_flag, $run_timestamp,
                $data[$map['handle']], $data[$map['title']], $data[$map['body']], $data[$map['vendor']], $data[$map['type']], $data[$map['tags']],
                $variantSkuGroup, $data[$map['barcode']], $data[$map['grams']],
                $data[$map['img1']], $data[$map['img2']], $data[$map['img3']],
                $data[$map['opt1_name']], $option1Val, $data[$map['opt2_name']], $option2Val,
                $generated_sku
            );
            $stmt_update_db->execute();
            $total_updated++;
        } else {
            $shopifyGids = productQueryBySku_graphql($token, $shopurl, $generated_sku);
            $needs_update_flag = 0; $gid_product = null; $gid_variant = null; $gid_inventory = null;

            if ($shopifyGids === null) {
                $needs_update_flag = 2;
                $total_created++;
            } else {
                $needs_update_flag = 10;
                $gid_product = $shopifyGids['product_gid'];
                $gid_variant = $shopifyGids['variant_gid'];
                $gid_inventory = $shopifyGids['inventory_gid'];
                $total_adopted++;
            }

            $qty1 = ($feed['location_index'] == 1) ? $newQuantity : 0;
            $qty2 = ($feed['location_index'] == 2) ? $newQuantity : 0;

            $stmt_insert_db->bind_param(
                "sssssssssissssssssdiisssis",
                $data[$map['handle']], $data[$map['title']], $data[$map['body']], $data[$map['vendor']], $data[$map['type']], $data[$map['tags']],
                $variantSkuGroup, $generated_sku, $data[$map['barcode']], $data[$map['grams']], $data[$map['tracker']],
                $data[$map['img1']], $data[$map['img2']], $data[$map['img3']],
                $data[$map['opt1_name']], $option1Val, $data[$map['opt2_name']], $option2Val,
                $newPrice, $qty1, $qty2,
                $gid_product, $gid_variant, $gid_inventory,
                $needs_update_flag, $run_timestamp
            );

            if (!$stmt_insert_db->execute()) {
                if (strpos($stmt_insert_db->error, "Duplicate entry") !== false) {
                    echo "Információ: A '$generated_sku' már feldolgozva.<br>";
                } else {
                    echo "Hiba az INSERT során (SKU: $generated_sku): " . $stmt_insert_db->error . "<br>";
                }
            }
        }
        $result->close();
    }
    fclose($temp);
    echo "Feed feldolgozva.<br>";
}

$stmt_check_db->close();
$stmt_insert_db->close();
$stmt_update_db->close();

echo "<hr><h3>Eredmények (Adatbázis):</h3>";
echo "Feldolgozott sorok: $total_rows_processed<br>";
echo "Új termék (létrehozás): $total_created<br>";
echo "Örökbefogadott (javítás): $total_adopted<br>";
echo "Frissített: $total_updated<br>";

// --- 8. ARCHIVÁLÁSI LOGIKA ---
echo "<hr><h3>Archiválás futtatása...</h3>";
$archive_sql = "UPDATE shopifyproducts SET needs_update = 20 WHERE last_seen_in_feed < ? AND needs_update NOT IN (20, 2, 10)";
$stmt_archive = $conn->prepare($archive_sql);
$stmt_archive->bind_param("s", $run_timestamp);
$stmt_archive->execute();
$archived_count = $stmt_archive->affected_rows;
$stmt_archive->close();
echo "Archiválásra megjelölve: <b>$archived_count</b> termék.<br>";

echo "<h2>Befejezve: 1. LÉPÉS – BEOLVASÁS és SZINKRONIZÁLÁS</h2></pre>";
$conn->close();
?>
