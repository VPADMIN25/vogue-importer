<?php
// index3new.php – V24 – TELJES, JAVÍTOTT, TESZT MÓD (1 TERMÉK)
ini_set('max_execution_time', 1800);
set_time_limit(1800);
ini_set('memory_limit', '2G');

$start_time = time();
$max_runtime = 600; // 10 perc

echo "<pre style='font-family:Consolas;font-size:14px'>";
echo "<h2>3. LÉPÉS – MÓDOSÍTÁSOK ÉS TÖRLÉSEK (TELJES VERZIÓ)</h2>";

// --- KAPCSOLÓDÁS (RETRY) ---
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
    if ($conn) {
        echo "Kapcsolódva: {$env['DB_HOST']} (próbálkozás: " . ($i + 1) . ")\n";
        break;
    }
    echo "Kapcsolódás sikertelen... próbálkozás " . ($i + 1) . "/$maxRetries\n";
    sleep(5);
}
if (!$conn) die("FATAL: Nem sikerült kapcsolódni a MySQL-hez!");
mysqli_set_charset($conn, "utf8mb4");

// --- SHOPIFY + RAKTÁRAK ---
require_once("helpers/shopifyGraphQL.php");
$shopurl = getenv('SHOPIFY_SHOP_URL');
$token = getenv('SHOPIFY_API_TOKEN');
if (empty($shopurl) || empty($token)) die("Hiányzó SHOPIFY változók.");

$loc1 = getShopifyLocationGid($token, $shopurl, "Italy Vogue Premiere Warehouse 1");
$loc2 = getShopifyLocationGid($token, $shopurl, "Italy Vogue Premiere Warehouse 2");
if (!$loc1 || !$loc2) die("Raktárak hiányoznak!");

// --- CSAK 10 SOR (TESZT) ---
$sql = "SELECT * FROM shopifyproducts WHERE needs_update IN (1, 10, 20) AND shopifyproductid IS NOT NULL LIMIT 10";
$result = $conn->query($sql);

if (!$result || $result->num_rows == 0) {
    echo "Nincs frissíteni vagy törölni való. <b>Minden OK!</b>\n";
    $conn->close();
    exit;
}

echo "Feldolgozandó: " . $result->num_rows . " variáns\n";

// --- VÁLOGATÓ TÖMBÖK (MINDEN LOGIKA BENN VAN) ---
$variant_price_inventory_queue = []; // Ár + készlet
$product_update_queue = [];          // Cím, leírás, tags
$variant_full_update_queue = [];     // SKU, vonalkód, súly
$variant_delete_queue = [];          // Törlés
$inventory_queue = [];               // Készlet
$processed_ids = [];                 // DB frissítés

$count = 0;
while ($row = $result->fetch_assoc()) {
    $count++;
    $gid = $row['shopifyproductid'];
    $vid = $row['shopifyvariantid'];

    // --- FRISSÍTÉS (needs_update = 1 vagy 10) ---
    if (in_array($row['needs_update'], [1, 10])) {
        
        // 1. TERMÉK FŐ ADATAI (ÖNGYÓGYÍTÁS)
        if ($row['needs_update'] == 10) {
            $product_update_queue[$gid] = [
                "id" => $gid,
                "title" => trim($row['vendor'] ?? ''),
                "descriptionHtml" => $row['body'] ?? '',
                "vendor" => $row['vendor'] ?? 'Unknown',
                "productType" => $row['type'] ?? 'Clothing',
                "tags" => array_filter(array_map('trim', explode(',', $row['tags'] ?? '')))
            ];
            
            // 2. VARIÁNS TELJES FRISSÍTÉS (SKU, VONALKÓD, SÚLY)
            $variant_full_update_queue[$vid] = [
                "id" => $vid,
                "sku" => $row['generated_sku'] ?? '',
                "barcode" => $row['barcode'] ?? null,
                "weight" => (float)($row['grams'] / 1000 ?? 0),
                "weightUnit" => "KILOGRAMS"
            ];
        }
        
        // 3. ÁR ÉS KÉSZLET (MINDIG FRISSÜL)
        $variant_price_inventory_queue[$gid][] = [
            "id" => $vid,
            "price" => number_format((float)$row['price_huf'], 2, '.', '')
        ];
        
        $inventory_queue[] = [
            "inventoryItemId" => $row['shopifyinventoryid'],
            "locationId" => $loc1,
            "availableQuantity" => (int)$row['qty_location_1']
        ];
        $inventory_queue[] = [
            "inventoryItemId" => $row['shopifyinventoryid'],
            "locationId" => $loc2,
            "availableQuantity" => (int)$row['qty_location_2']
        ];

        $processed_ids[] = $row['id'];
    }

    // --- TÖRLÉS (needs_update = 20) ---
    if ($row['needs_update'] == 20) {
        $variant_delete_queue[] = $vid;
    }

    // --- IDŐKORLÁT ---
    if (time() - $start_time > $max_runtime) {
        echo "IDŐKORLÁT: 10 perc eltelt – $count variáns feldolgozva. Maradék a következő futásra.\n";
        break;
    }
}

// ========================================
// 1. ÁRVA VARIÁNSOK TÖRLÉSE
// ========================================
if (!empty($variant_delete_queue)) {
    $unique_vids = array_unique($variant_delete_queue);
    echo "Törlendő variánsok: " . count($unique_vids) . "\n";
    
    foreach ($unique_vids as $vid) {
        $resp = productVariantDelete_graphql($token, $shopurl, $vid);
        if (!empty($resp['data']['productVariantDelete']['deletedProductVariantId'])) {
            echo "TÖRÖLVE (Shopify): $vid\n";
        } else {
            echo "HIBA (törlés): $vid\n";
        }
    }
    
    // DB-ből is törlés
    $vids_str = "'" . implode("','", $unique_vids) . "'";
    $conn->query("DELETE FROM shopifyproducts WHERE shopifyvariantid IN ($vids_str)");
    echo "TÖRÖLVE (DB): " . count($unique_vids) . " variáns\n";
}

// ========================================
// 2. TERMÉK TELJES FRISSÍTÉS (CÍM, TAGEK, STB.)
// ========================================
if (!empty($product_update_queue)) {
    echo "Termék frissítések: " . count($product_update_queue) . "\n";
    foreach ($product_update_queue as $gid => $input) {
        $resp = productFullUpdate_graphql($token, $shopurl, $gid, $input);
        if (!empty($resp['data']['productUpdate']['product']['id'])) {
            echo "FRISSÍTVE (Termék): $gid\n";
        } else {
            echo "HIBA (termék frissítés): $gid\n";
        }
    }
}

// ========================================
// 3. VARIÁNS TELJES FRISSÍTÉS (SKU, VONALKÓD)
// ========================================
if (!empty($variant_full_update_queue)) {
    echo "Variáns teljes frissítések: " . count($variant_full_update_queue) . "\n";
    foreach ($variant_full_update_queue as $vid => $input) {
        $resp = productVariantFullUpdate_graphql($token, $shopurl, $input);
        if (!empty($resp['data']['productVariantUpdate']['productVariant']['id'])) {
            echo "FRISSÍTVE (Variáns): $vid\n";
        } else {
            echo "HIBA (variáns frissítés): $vid\n";
        }
    }
}

// ========================================
// 4. ÁR FRISSÍTÉS + REAKTIVÁLÁS
// ========================================
if (!empty($variant_price_inventory_queue)) {
    echo "Ár frissítések: " . count($variant_price_inventory_queue) . " termék\n";
    foreach ($variant_price_inventory_queue as $gid => $variants) {
        $resp = productVariantsBulkUpdate_graphql($token, $shopurl, $gid, $variants);
        if (!empty($resp['data']['productVariantsBulkUpdate']['productVariants'])) {
            echo "ÁR FRISSÍTVE: $gid\n";
            productUpdateStatus_graphql($token, $shopurl, $gid, 'ACTIVE');
            echo "AKTIVÁLVA: $gid\n";
        } else {
            echo "HIBA (ár frissítés): $gid\n";
        }
    }
}

// ========================================
// 5. KÉSZLET FRISSÍTÉS
// ========================================
if (!empty($inventory_queue)) {
    $chunks = array_chunk($inventory_queue, 100);
    echo "Készlet frissítések: " . count($inventory_queue) . " tétel (" . count($chunks) . " chunk)\n";
    foreach ($chunks as $i => $chunk) {
        $resp = inventorySetQuantities_graphql($token, $shopurl, $chunk);
        if (!empty($resp['data']['inventorySetQuantities']['userErrors'])) {
            echo "HIBA (készlet chunk $i)\n";
        } else {
            echo "KÉSZLET FRISSÍTVE (chunk $i)\n";
        }
    }
}

// ========================================
// 6. DB ZÁSZLÓK TÖRLÉSE
// ========================================
if (!empty($processed_ids)) {
    $ids_str = implode(',', array_unique($processed_ids));
    $conn->query("UPDATE shopifyproducts SET needs_update = 0 WHERE id IN ($ids_str)");
    echo "DB FRISSÍTVE: " . count($processed_ids) . " sor (needs_update = 0)\n";
}

// ========================================
// VÉGE
// ========================================
echo "<hr><h2>3. LÉPÉS KÉSZ – $count variáns feldolgozva</h2></pre>";
$conn->close();
?>
