<?php
// index3new.php – V24 – VÉGLEGES (Időlimit + Variáns Javítás + Variáns Törlés)
ini_set('max_execution_time', 1800);
set_time_limit(1800);
ini_set('memory_limit', '2G');

$start_time = time();
$max_runtime = 600; // 10 perc, hogy a 15 perces futásba beleférjen

echo "<pre style='font-family:Consolas;font-size:14px'>";
echo "<h2>3. LÉPÉS – MÓDOSÍTÁSOK (VÉGLEGES V24)</h2>";

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

// === NINCS TÖBBÉ LIMIT 200 ===
$sql = "SELECT * FROM shopifyproducts WHERE needs_update IN (1, 10, 20) AND shopifyproductid IS NOT NULL";
$result = $conn->query($sql);

if (!$result || $result->num_rows == 0) {
    echo "Nincs frissíteni vagy törölni való variáns. <b>Minden OK!</b>\n";
    $conn->close();
    exit;
}

// === VÁLOGATÓ TÖMBÖK ===
$variant_price_inventory_queue = []; // Gyors ár/készlet
$product_update_queue = [];          // Cím, Tagek
$variant_full_update_queue = [];     // Lassú SKU, Vonalkód (Öngyógyítás)
$variant_delete_queue = [];          // Árva variánsok törlése
$inventory_queue = [];
$processed_ids = [];

$count = 0;

while ($row = $result->fetch_assoc()) {
    $gid = $row['shopifyproductid'];
    $vid = $row['shopifyvariantid'];

    // FRISSÍTÉS ÉS REAKTIVÁLÁS
    if (in_array($row['needs_update'], [1, 10])) {
        
        // ÖNGYÓGYÍTÁS (HIÁNYOS ADATOK PÓTLÁSA)
        if ($row['needs_update'] == 10) {
            // 1. Termék fő adatai (Cím, Tagek, stb.)
            $product_update_queue[$gid] = [
                "id" => $gid,
                "title" => trim($row['vendor'] ?? ''), // CÍM = VENDOR!
                "descriptionHtml" => $row['body'] ?? '',
                "vendor" => $row['vendor'] ?? 'Unknown',
                "productType" => $row['type'] ?? 'Clothing',
                "tags" => array_filter(array_map('trim', explode(',', $row['tags'] ?? '')))
            ];
            
            // 2. Variáns fő adatai (SKU, Vonalkód, Súly)
            $variant_full_update_queue[$vid] = [
                "id" => $vid,
                "sku" => $row['generated_sku'] ?? '',
                "barcode" => $row['barcode'] ?? null,
                "weight" => (float)($row['grams'] / 1000 ?? 0),
                "weightUnit" => "KILOGRAMS"
            ];
        }
        
        // 3. Ár és Készlet (Ezt mindig frissítjük)
        $variant_price_inventory_queue[$gid][] = ["id" => $vid, "price" => $row['price_huf']];
        $inventory_queue[] = ["inventoryItemId" => $row['shopifyinventoryid'], "locationId" => $loc1, "availableQuantity" => $row['qty_location_1']];
        $inventory_queue[] = ["inventoryItemId" => $row['shopifyinventoryid'], "locationId" => $loc2, "availableQuantity" => $row['qty_location_2']];

        $processed_ids[] = $row['id'];
    }

    // ÁRVA VARIÁNS TÖRLÉSE
    if ($row['needs_update'] == 20) {
        $variant_delete_queue[] = $vid;
    }
    
    $count++;
    if (time() - $start_time > $max_runtime) {
        echo "IDŐKORLÁT: 10 perc eltelt – $count variáns feldolgozva. Maradék a következő futásra.\n";
        break; // Kilépünk a while ciklusból, hogy a maradékot feldolgozzuk
    }
}

// --- VÉGREHAJTÁS ---

// 1. ÁRVA VARIÁNSOK TÖRLÉSE (Ezzel törlődik az "L"-es méret)
foreach (array_unique($variant_delete_queue) as $vid) {
    productVariantDelete_graphql($token, $shopurl, $vid);
    echo "TÖRÖLVE (Variáns): $vid\n";
}
// Töröljük a helyi adatbázisból is
if (!empty($variant_delete_queue)) {
    $vids_to_delete_str = "'" . implode("','", array_unique($variant_delete_queue)) . "'";
    $conn->query("DELETE FROM shopifyproducts WHERE shopifyvariantid IN ($vids_to_delete_str)");
    echo "TÖRÖLVE (Helyi DB): " . count($variant_delete_queue) . " variáns\n";
}

// 2. TELJES TERMÉK FRISSÍTÉS (TAGEK, LEÍRÁS, STB.)
foreach ($product_update_queue as $gid => $input) {
    productFullUpdate_graphql($token, $shopurl, $gid, $input);
    echo "TELJES FRISSÍTÉS (Tagek, Cím): $gid\n";
}

// 3. TELJES VARIÁNS FRISSÍTÉS (SKU, VONALKÓD)
foreach ($variant_full_update_queue as $vid => $input) {
    productVariantFullUpdate_graphql($token, $shopurl, $input);
    echo "VARIÁNS FRISSÍTÉS (SKU): $vid\n";
}

// 4. ÁR FRISSÍTÉS (és REAKTIVÁLÁS)
foreach ($variant_price_inventory_queue as $gid => $variants) {
    productVariantsBulkUpdate_graphql($token, $shopurl, $gid, $variants);
    productUpdateStatus_graphql($token, $shopurl, $gid, 'ACTIVE'); // Reaktiválás
    echo "ÁR FRISSÍTVE (és Aktiválva): $gid\n";
}

// 5. KÉSZLET FRISSÍTÉS
foreach (array_chunk($inventory_queue, 100) as $chunk) {
    inventorySetQuantities_graphql($token, $shopurl, $chunk);
}
echo "KÉSZLET FRISSÍTVE: " . count($inventory_queue) . " tétel\n";

// 6. FRISSÍTETTEK ZÁSZLÓJÁNAK TÖRLÉSE
if (!empty($processed_ids)) {
    $ids_to_update = implode(',', array_unique($processed_ids));
    $conn->query("UPDATE shopifyproducts SET needs_update = 0 WHERE id IN ($ids_to_update)");
    echo "ADATBÁZIS FRISSÍTVE: " . count($processed_ids) . " sor (needs_update=0)\n";
}

echo "<h2>3. LÉPÉS KÉSZ</h2></pre>";
$conn->close();
?>
