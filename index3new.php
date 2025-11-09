<?php
// index3new.php – V23 – VÉGLEGES (Törlés Shopify-ból ÉS Helyi DB-ből + Reaktiválás + Öngyógyítás)
ini_set('max_execution_time', 1800);
set_time_limit(1800);
ini_set('memory_limit', '2G');

echo "<pre style='font-family:Consolas;font-size:14px'>";
echo "<h2>3. LÉPÉS – MÓDOSÍTÁSOK (VÉGLEGES V23)</h2>";

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

if (!$result || $result->num_rows == 0) {
    echo "Nincs frissíteni vagy törölni való termék. <b>Minden OK!</b>\n";
    $conn->close();
    exit;
}

// === VÁLOGATÓ TÖMBÖK ===
$variant_update_queue = [];
$inventory_queue = [];
$product_update_queue = [];
$delete_shopify_queue = []; // Shopify GID-ek törlésre
$delete_local_ids = [];     // Helyi DB ID-k törlésre
$processed_ids = [];        // Helyi DB ID-k frissítésre (needs_update=0)

while ($row = $result->fetch_assoc()) {
    $gid = $row['shopifyproductid'];

    // FRISSÍTÉS ÉS REAKTIVÁLÁS
    if (in_array($row['needs_update'], [1, 10])) {
        // Ár és Készlet
        $variant_update_queue[$gid][] = ["id" => $row['shopifyvariantid'], "price" => $row['price_huf']];
        $inventory_queue[] = ["inventoryItemId" => $row['shopifyinventoryid'], "locationId" => $loc1, "availableQuantity" => $row['qty_location_1']];
        $inventory_queue[] = ["inventoryItemId" => $row['shopifyinventoryid'], "locationId" => $loc2, "availableQuantity" => $row['qty_location_2']];

        // "ÖNGYÓGYÍTÁS": Hiányzó Tagek, Cím, stb. pótlása
        if ($row['needs_update'] == 10) {
            $product_update_queue[$gid] = [
                "id" => $gid,
                "title" => trim($row['vendor'] ?? ''), // CÍM = VENDOR!
                "descriptionHtml" => $row['body'] ?? '',
                "vendor" => $row['vendor'] ?? 'Unknown',
                "productType" => $row['type'] ?? 'Clothing',
                "tags" => array_filter(array_map('trim', explode(',', $row['tags'] ?? '')))
            ];
        }
        $processed_ids[] = $row['id']; // Ezeket a végén 0-ra állítjuk
    }

    // VÉGLEGES TÖRLÉS
    if ($row['needs_update'] == 20) {
        $delete_shopify_queue[] = $gid;
        $delete_local_ids[] = $row['id'];
    }
}

// --- VÉGREHAJTÁS ---

// 1. TÖRLÉS (Shopify-ból)
foreach (array_unique($delete_shopify_queue) as $gid) {
    send_graphql_request($token, $shopurl, "mutation { productDelete(input: {id: \"$gid\"}) { deletedProductId } }");
    echo "TÖRÖLVE (Shopify): $gid\n";
}

// 2. TÖRLÉS (Helyi Adatbázisból)
if (!empty($delete_local_ids)) {
    $ids_to_delete = implode(',', array_unique($delete_local_ids));
    $conn->query("DELETE FROM shopifyproducts WHERE id IN ($ids_to_delete)");
    echo "TÖRÖLVE (Helyi DB): " . count($delete_local_ids) . " sor\n";
}

// 3. TELJES TERMÉK FRISSÍTÉS (TAGEK, LEÍRÁS, STB.)
foreach ($product_update_queue as $gid => $input) {
    productFullUpdate_graphql($token, $shopurl, $gid, $input);
    echo "TELJES FRISSÍTÉS (Tagek, Cím): $gid\n";
}

// 4. ÁR ÉS KÉSZLET FRISSÍTÉS (és REAKTIVÁLÁS)
foreach ($variant_update_queue as $gid => $variants) {
    productVariantsBulkUpdate_graphql($token, $shopurl, $gid, $variants);
    productUpdateStatus_graphql($token, $shopurl, $gid, 'ACTIVE'); // Reaktiválás
    echo "ÁR/KÉSZLET FRISSÍTVE: $gid\n";
}
// Készlet beállítása
foreach (array_chunk($inventory_queue, 100) as $chunk) {
    inventorySetQuantities_graphql($token, $shopurl, $chunk);
}

// 5. FRISSÍTETTEK ZÁSZLÓJÁNAK TÖRLÉSE
if (!empty($processed_ids)) {
    $ids_to_update = implode(',', array_unique($processed_ids));
    $conn->query("UPDATE shopifyproducts SET needs_update = 0 WHERE id IN ($ids_to_update)");
}

echo "<h2>3. LÉPÉS KÉSZ</h2></pre>";
$conn->close();
?>
