<?php
// index3new.php – V20 – CÍM = VENDOR
ini_set('max_execution_time', 1800);
set_time_limit(1800);
ini_set('memory_limit', '2G');

echo "<pre style='font-family:Consolas;font-size:14px'>";
echo "<h2>3. LÉPÉS – MÓDOSÍTÁSOK</h2>";

// --- KAPCSOLAT ---
$env = [
    'DB_HOST' => getenv('DB_HOST'),
    'DB_USER' => getenv('DB_USER'),
    'DB_PASS' => getenv('DB_PASS'),
    'DB_NAME' => getenv('DB_NAME'),
    'DB_PORT' => (int)getenv('DB_PORT')
];

$conn = null;
$maxRetries = 5;
for ($i = 0; $i < $maxRetries; $i++) {
    $conn = @mysqli_connect($env['DB_HOST'], $env['DB_USER'], $env['DB_PASS'], $env['DB_NAME'], (int)$env['DB_PORT']);
    if ($conn) break;
    echo "Kapcsolódás sikertelen... próbálkozás " . ($i + 1) . "\n";
    sleep(5);
}
if (!$conn) die("FATAL: MySQL hiba!");
mysqli_set_charset($conn, "utf8mb4");

require_once("helpers/shopifyGraphQL.php");
$shopurl = getenv('SHOPIFY_SHOP_URL');
$token = getenv('SHOPIFY_API_TOKEN');
$loc1 = getShopifyLocationGid($token, $shopurl, "Italy Vogue Premiere Warehouse 1");
$loc2 = getShopifyLocationGid($token, $shopurl, "Italy Vogue Premiere Warehouse 2");

$sql = "SELECT * FROM shopifyproducts WHERE needs_update IN (1, 10, 20) AND shopifyproductid IS NOT NULL LIMIT 200";
$result = $conn->query($sql);

$update_queue = []; $inventory_queue = []; $archive_queue = []; $delete_queue = [];

while ($row = $result->fetch_assoc()) {
    $gid = $row['shopifyproductid'];

    // DRAFT TÖRLÉSE
    if ($row['needs_update'] == 2) {
        $delete_queue[] = $gid;
        continue;
    }

    // FRISSÍTÉS
    if (in_array($row['needs_update'], [1, 10])) {
        $update_queue[$gid][] = ["id" => $row['shopifyvariantid'], "price" => $row['price_huf']];
        $inventory_queue[] = ["inventoryItemId" => $row['shopifyinventoryid'], "locationId" => $loc1, "availableQuantity" => $row['qty_location_1']];
        $inventory_queue[] = ["inventoryItemId" => $row['shopifyinventoryid'], "locationId" => $loc2, "availableQuantity" => $row['qty_location_2']];
    }

    // ARCHIVÁLÁS
    if ($row['needs_update'] == 20) {
        $archive_queue[] = $gid;
    }
}

// TÖRLÉS
foreach ($delete_queue as $gid) {
    send_graphql_request($token, $shopurl, "mutation { productDelete(input: {id: \"$gid\"}) { deletedProductId } }");
    echo "DRAFT TÖRÖLVE: $gid\n";
}

// FRISSÍTÉS
foreach ($update_queue as $gid => $variants) {
    productVariantsBulkUpdate_graphql($token, $shopurl, $gid, $variants);
    productUpdateStatus_graphql($token, $shopurl, $gid, 'ACTIVE');
}
foreach (array_chunk($inventory_queue, 100) as $chunk) {
    inventorySetQuantities_graphql($token, $shopurl, $chunk);
}

// ARCHIVÁLÁS
foreach ($archive_queue as $gid) {
    productUpdateStatus_graphql($token, $shopurl, $gid, 'ARCHIVED');
}

// needs_update = 0
$ids = implode(',', array_column($result->fetch_all(MYSQLI_ASSOC), 'id'));
$conn->query("UPDATE shopifyproducts SET needs_update = 0 WHERE id IN ($ids)");

echo "<h2>3. LÉPÉS KÉSZ</h2></pre>";
$conn->close();
?>

