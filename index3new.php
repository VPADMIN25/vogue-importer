<?php
// index3new.php (V4 - DigitalOcean Kompatibilis)

ini_set('max_execution_time', 0);
set_time_limit(0);
echo "<pre style='font-family:Consolas;font-size:14px'>";
echo "<h2>FUTÁS INDUL: 3. LÉPÉS - MÓDOSÍTÁSOK VÉGREHAJTÁSA</h2>";

// --- KAPCSOLAT (RETRY) ---
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
    if ($env['DB_SSLMODE'] === 'require') mysqli_options($conn, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);
    if (@mysqli_real_connect($conn, $env['DB_HOST'], $env['DB_USER'], $env['DB_PASS'], $env['DB_NAME'], $env['DB_PORT'], NULL, MYSQLI_CLIENT_SSL)) break;
    echo "Kapcsolódás sikertelen... próbálkozás " . ($i + 1) . "/$maxRetries\n";
    sleep(5);
}
if (!$conn) die("FATAL: MySQL hiba!");
mysqli_set_charset($conn, "utf8mb4");
echo "Adatbázis-kapcsolat sikeres.<br>";

require_once("helpers/shopifyGraphQL.php");
$shopurl = getenv('SHOPIFY_SHOP_URL');
$token = getenv('SHOPIFY_API_TOKEN');
if (empty($shopurl) || empty($token)) die("Hiányzó SHOPIFY változók.");

$location_gid_1 = getShopifyLocationGid($token, $shopurl, "Italy Vogue Premiere Warehouse 1");
$location_gid_2 = getShopifyLocationGid($token, $shopurl, "Italy Vogue Premiere Warehouse 2");
if (empty($location_gid_1) || empty($location_gid_2)) die("Raktárak hiányoznak!");

$sql = "SELECT * FROM shopifyproducts WHERE needs_update IN (1, 10, 20) AND shopifyproductid IS NOT NULL LIMIT 200";
$result = $conn->query($sql);
if (!$result || $result->num_rows == 0) {
    echo "Nincs tennivaló.<br>";
    $conn->close();
    exit;
}
echo "Feldolgozás: <b>{$result->num_rows}</b> tétel...<br>";

$inventory_update_queue = [];
$price_update_queue = [];
$status_archive_queue = [];
$status_reactivate_queue = [];
$full_overwrite_queue = [];
$processed_ids_success = [];

while ($row = $result->fetch_assoc()) {
    switch ($row['needs_update']) {
        case 1:
            $product_gid = $row['shopifyproductid'];
            if (!isset($price_update_queue[$product_gid])) $price_update_queue[$product_gid] = [];
            $price_update_queue[$product_gid][] = ["id" => $row['shopifyvariantid'], "price" => $row['price_huf']];
            $inventory_update_queue[] = ["inventoryItemId" => $row['shopifyinventoryid'], "locationId" => $location_gid_1, "availableQuantity" => (int)$row['qty_location_1']];
            $inventory_update_queue[] = ["inventoryItemId" => $row['shopifyinventoryid'], "locationId" => $location_gid_2, "availableQuantity" => (int)$row['qty_location_2']];
            $status_reactivate_queue[] = $product_gid;
            $processed_ids_success[] = $row['id'];
            break;
        case 10:
            $full_overwrite_queue[] = $row;
            break;
        case 20:
            $status_archive_queue[] = $row['shopifyproductid'];
            break;
    }
}

if (!empty($price_update_queue)) {
    echo "<hr><h4>Árak frissítése...</h4>";
    foreach ($price_update_queue as $product_gid => $variants) {
        productVariantsBulkUpdate_graphql($token, $shopurl, $product_gid, $variants);
    }
}

if (!empty($inventory_update_queue)) {
    echo "<hr><h4>Készletek frissítése...</h4>";
    foreach (array_chunk($inventory_update_queue, 100) as $chunk) {
        inventorySetQuantities_graphql($token, $shopurl, $chunk);
    }
}

if (!empty($status_reactivate_queue)) {
    echo "<hr><h4>Reaktiválás...</h4>";
    foreach (array_unique($status_reactivate_queue) as $product_gid) {
        productUpdateStatus_graphql($token, $shopurl, $product_gid, 'ACTIVE');
    }
}

if (!empty($status_archive_queue)) {
    echo "<hr><h4>Archiválás...</h4>";
    foreach (array_unique($status_archive_queue) as $product_gid) {
        productUpdateStatus_graphql($token, $shopurl, $product_gid, 'ARCHIVED');
    }
}

if (!empty($full_overwrite_queue)) {
    echo "<hr><h4>Teljes felülírás...</h4>";
    foreach ($full_overwrite_queue as $row) {
        $product_data = [
            "id" => $row['shopifyproductid'],
            "title" => $row['title'],
            "bodyHtml" => $row['body'],
            "vendor" => $row['vendor'],
            "productType" => $row['type'],
            "tags" => $row['tags'],
            "status" => "ACTIVE"
        ];
        if (!empty($row['img_src'])) $product_data["images"] = [["src" => $row['img_src']]];
        if (!empty($row['img_src_2'])) $product_data["images"][] = ["src" => $row['img_src_2']];
        if (!empty($row['img_src_3'])) $product_data["images"][] = ["src" => $row['img_src_3']];

        productFullUpdate_graphql($token, $shopurl, $row['shopifyproductid'], $product_data);

        productVariantsBulkUpdate_graphql($token, $shopurl, $row['shopifyproductid'], [["id" => $row['shopifyvariantid'], "price" => $row['price_huf']]]);

        $inventory_data = [
            ["inventoryItemId" => $row['shopifyinventoryid'], "locationId" => $location_gid_1, "availableQuantity" => (int)$row['qty_location_1']],
            ["inventoryItemId" => $row['shopifyinventoryid'], "locationId" => $location_gid_2, "availableQuantity" => (int)$row['qty_location_2']]
        ];
        inventorySetQuantities_graphql($token, $shopurl, $inventory_data);

        $processed_ids_success[] = $row['id'];
    }
}

if (!empty($processed_ids_success)) {
    $ids_string = implode(',', $processed_ids_success);
    $conn->query("UPDATE shopifyproducts SET needs_update = 0 WHERE id IN ($ids_string)");
    echo "<hr>Sikeresen frissítve: " . count($processed_ids_success) . " tétel.<br>";
}

echo "<h2>Befejezve: 3. LÉPÉS</h2></pre>";
$conn->close();
?>
