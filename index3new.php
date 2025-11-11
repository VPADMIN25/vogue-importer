<?php
// index3new.php – TESZT MÓD (1 TERMÉK)
ini_set('max_execution_time', 1800);
set_time_limit(1800);
ini_set('memory_limit', '2G');

echo "<pre style='font-family:Consolas;font-size:14px'>";
echo "<h2>3. LÉPÉS – FRISSÍTÉS/TÖRLÉS (TESZT)</h2>";

$env = ['DB_HOST' => getenv('DB_HOST'), 'DB_USER' => getenv('DB_USER'), 'DB_PASS' => getenv('DB_PASS'), 'DB_NAME' => getenv('DB_NAME'), 'DB_PORT' => (int)getenv('DB_PORT')];

$conn = @mysqli_connect($env['DB_HOST'], $env['DB_USER'], $env['DB_PASS'], $env['DB_NAME'], (int)$env['DB_PORT']);
if (!$conn) die("FATAL: MySQL hiba!");
mysqli_set_charset($conn, "utf8mb4");

require_once("helpers/shopifyGraphQL.php");
$shopurl = getenv('SHOPIFY_SHOP_URL');
$token = getenv('SHOPIFY_API_TOKEN');
$loc1 = getShopifyLocationGid($token, $shopurl, "Italy Vogue Premiere Warehouse 1");
$loc2 = getShopifyLocationGid($token, $shopurl, "Italy Vogue Premiere Warehouse 2");

$sql = "SELECT * FROM shopifyproducts WHERE needs_update IN (1, 10, 20) AND shopifyproductid IS NOT NULL LIMIT 10";
$result = $conn->query($sql);

if (!$result || $result->num_rows == 0) {
    echo "Nincs frissíteni/törölni való. <b>Minden OK!</b>\n";
    $conn->close();
    exit;
}

$variant_price_inventory_queue = [];
$product_update_queue = [];
$variant_full_update_queue = [];
$variant_delete_queue = [];
$inventory_queue = [];
$processed_ids = [];

while ($row = $result->fetch_assoc()) {
    $gid = $row['shopifyproductid'];
    $vid = $row['shopifyvariantid'];

    if (in_array($row['needs_update'], [1, 10])) {
        if ($row['needs_update'] == 10) {
            $product_update_queue[$gid] = [
                "id" => $gid,
                "title" => trim($row['vendor'] ?? ''),
                "descriptionHtml" => $row['body'] ?? '',
                "vendor" => $row['vendor'] ?? 'Unknown',
                "productType" => $row['type'] ?? 'Clothing',
                "tags" => array_filter(array_map('trim', explode(',', $row['tags'] ?? '')))
            ];
            $variant_full_update_queue[$vid] = [
                "id" => $vid,
                "sku" => $row['generated_sku'] ?? '',
                "barcode" => $row['barcode'] ?? null,
                "weight" => (float)($row['grams'] / 1000 ?? 0),
                "weightUnit" => "KILOGRAMS"
            ];
        }
        $variant_price_inventory_queue[$gid][] = ["id" => $vid, "price" => $row['price_huf']];
        $inventory_queue[] = ["inventoryItemId" => $row['shopifyinventoryid'], "locationId" => $loc1, "availableQuantity" => $row['qty_location_1']];
        $inventory_queue[] = ["inventoryItemId" => $row['shopifyinventoryid'], "locationId" => $loc2, "availableQuantity" => $row['qty_location_2']];
        $processed_ids[] = $row['id'];
    }

    if ($row['needs_update'] == 20) {
        $variant_delete_queue[] = $vid;
    }
}

// TÖRLÉS
foreach (array_unique($variant_delete_queue) as $vid) {
    productVariantDelete_graphql($token, $shopurl, $vid);
    echo "TÖRÖLVE: $vid\n";
}
if (!empty($variant_delete_queue)) {
    $vids_str = "'" . implode("','", array_unique($variant_delete_queue)) . "'";
    $conn->query("DELETE FROM shopifyproducts WHERE shopifyvariantid IN ($vids_str)");
}

// FRISSÍTÉS
foreach ($product_update_queue as $gid => $input) {
    productFullUpdate_graphql($token, $shopurl, $gid, $input);
}
foreach ($variant_full_update_queue as $vid => $input) {
    productVariantFullUpdate_graphql($token, $shopurl, $input);
}
foreach ($variant_price_inventory_queue as $gid => $variants) {
    productVariantsBulkUpdate_graphql($token, $shopurl, $gid, $variants);
    productUpdateStatus_graphql($token, $shopurl, $gid, 'ACTIVE');
}
foreach (array_chunk($inventory_queue, 100) as $chunk) {
    inventorySetQuantities_graphql($token, $shopurl, $chunk);
}

if (!empty($processed_ids)) {
    $ids_str = implode(',', array_unique($processed_ids));
    $conn->query("UPDATE shopifyproducts SET needs_update = 0 WHERE id IN ($ids_str)");
}

echo "<h2>3. LÉPÉS KÉSZ</h2></pre>";
$conn->close();
?>
