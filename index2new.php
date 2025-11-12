<?php
// index2new.php – VÉGLEGES JAVÍTOTT VERZIÓ (warningok fix, debug echo-k, descriptionHtml fix, bulk create error handling)

ini_set('max_execution_time', 900);  // 15 perc
set_time_limit(900);
ini_set('memory_limit', '2G');

echo "<pre style='font-family:Consolas;font-size:14px'>";
echo "<h2>2. LÉPÉS – ÚJ TERMÉKEK LÉTREHOZÁSA (TELJES VERZIÓ)</h2>";

// --- KAPCSOLAT (RETRY) ---
$env = [
    'DB_HOST' => getenv('DB_HOST'),
    'DB_USER' => getenv('DB_USER'),
    'DB_PASS' => getenv('DB_PASS'),
    'DB_NAME' => getenv('DB_NAME'),
    'DB_PORT' => (int)getenv('DB_PORT')
];

$conn = null;
$maxRetries = 12;
for ($i = 0; $i < $maxRetries; $i++) {
    $conn = @mysqli_connect($env['DB_HOST'], $env['DB_USER'], $env['DB_PASS'], $env['DB_NAME'], (int)$env['DB_PORT']);
    if ($conn) {
        echo "Kapcsolódva: {$env['DB_HOST']} (próbálkozás: " . ($i + 1) . ")\n";
        break;
    }
    echo "Kapcsolódás sikertelen... próbálkozás " . ($i + 1) . "/$maxRetries (15mp várakozás)\n";
    sleep(15);
}
if (!$conn) die("FATAL: Nem sikerült kapcsolódni a MySQL-hez 12 próbálkozás után!");
mysqli_set_charset($conn, "utf8mb4");

// --- SHOPIFY + RAKTÁRAK ---
require_once("helpers/shopifyGraphQL.php");
$shopurl = getenv('SHOPIFY_SHOP_URL');
$token = getenv('SHOPIFY_API_TOKEN');
if (empty($shopurl) || empty($token)) die("Hiányzó SHOPIFY változó: shopurl vagy token üres!");

echo "Shopify URL: $shopurl | Token: " . (empty($token) ? 'ÜRES!' : 'OK') . "\n";

$loc1 = getShopifyLocationGid($token, $shopurl, "Italy Vogue Premiere Warehouse 1");
$loc2 = getShopifyLocationGid($token, $shopurl, "Italy Vogue Premiere Warehouse 2");
if (!$loc1 || !$loc2) die("Raktárak hiányoznak! loc1: $loc1, loc2: $loc2");

// --- IDŐKORLÁT ---
$start_time = time();
$max_runtime = 600;  // 10 perc
$created_count = 0;

// --- CSAK ÚJ TERMÉKEK (TESZT: LIMIT 1, ÉLES: NINCS LIMIT) ---
$sql = "SELECT DISTINCT variant_sku FROM shopifyproducts WHERE needs_update=2 AND variant_sku!='' AND vendor!='' LIMIT 1"; // TESZT MÓD
// $sql = "SELECT DISTINCT variant_sku FROM shopifyproducts WHERE needs_update=2 AND variant_sku!='' AND vendor!=''"; // ÉLES
$groups = $conn->query($sql);

if (!$groups || $groups->num_rows == 0) {
    echo "Nincs új termék. <b>Minden OK!</b>\n";
    $conn->close();
    exit;
}

echo "Új termékcsoportok száma: " . $groups->num_rows . "\n";

while ($g = $groups->fetch_assoc()) {
    $sku_group = $g['variant_sku'];
    echo "Feldolgozás: SKU csoport - $sku_group\n";
    $group_sql = "SELECT * FROM shopifyproducts WHERE variant_sku = '$sku_group' AND needs_update=2";
    $group = $conn->query($group_sql);

    if (!$group || $group->num_rows == 0) continue;

    // --- ALAP ADATOK (első sor) ---
    $first = $group->fetch_assoc();
    $group->data_seek(0); // Reset

    $input = [
        'title' => $first['title'] ?? '',
        'descriptionHtml' => $first['body_html'] ?? '',  // Javítva: descriptionHtml
        'vendor' => $first['vendor'] ?? '',
        'productType' => $first['type'] ?? '',
        'tags' => explode(',', $first['tags'] ?? ''),
        'status' => 'DRAFT'
    ];

    $handle_base = sanitize_handle($first['handle'] ?? $first['title'] ?? 'product');
    $handle = $handle_base;
    $media = array_map(fn($url) => ['originalSource' => $url, 'mediaContentType' => 'IMAGE'], array_filter([
        $first['image1'] ?? '',
        $first['image2'] ?? '',
        $first['image3'] ?? ''
    ]));

    // --- VARIÁNSOK + OPCIÓK (javított: ?? defaultok) ---
    $variants = [];
    $option1_values = [];
    $option2_values = [];

    while ($row = $group->fetch_assoc()) {
        $variants[] = [
            'sku' => $row['generated_sku'] ?? '',
            'price' => number_format(($row['price_huf'] ?? 0) / 100, 2, '.', ''),
            'barcode' => $row['variant_barcode'] ?? '',
            'weight' => (float)($row['variant_grams'] ?? 0),
            'weightUnit' => 'GRAMS',
            'taxable' => true,
            'requiresShipping' => true,
            'option1' => trim($row['option1_value'] ?? ''),
            'option2' => trim($row['option2_value'] ?? ''),
            'qty1' => $row['qty1'] ?? 0,
            'qty2' => $row['qty2'] ?? 0,
        ];

        if (trim($row['option1_value'] ?? '') !== '') $option1_values[] = trim($row['option1_value']);
        if (trim($row['option2_value'] ?? '') !== '') $option2_values[] = trim($row['option2_value']);
    }

    $option1 = array_unique(array_filter($option1_values));
    $option2 = array_unique(array_filter($option2_values));

    $productOptions = [];
    if (count($option1) > 0) $productOptions[] = ['name' => $first['option1_name'] ?? 'Size', 'values' => $option1];
    if (count($option2) > 0) $productOptions[] = ['name' => $first['option2_name'] ?? 'Color', 'values' => $option2];

    // --- TERMÉK LÉTREHOZÁS (HANDLE DUPLIKÁCIÓ KEZELÉS HATÉKONYAN) ---
    $handle_base = sanitize_handle($first['handle'] ?? $first['title'] ?? 'product');
    $max_tries = 50;
    $t = 0;
    $handle = null;
    
    echo "Egyedi handle keresése...<br>";
    while ($t < $max_tries && !$handle) {
        $t++;
        $current_handle = ($t > 1) ? $handle_base . '-' . $t : $handle_base;
        if (isHandleAvailable_graphql($token, $shopurl, $current_handle)) {
            $handle = $current_handle;
            echo "Szabad handle talált: $handle (próbálkozás: $t)<br>";
        } else {
            echo "Handle foglalt: $current_handle (próbálkozás: $t/$max_tries)<br>";
        }
    }
    
    if (!$handle) {
        echo "Nem talált szabad handle-t $max_tries próbálkozás után.<br>";
        continue;
    }
    
    // Most állítsuk be a handle-t és hozzuk létre a terméket (csak egyszer)
    $input['handle'] = $handle;
    echo "Termék létrehozása (Handle: $handle)...<br>";
    $resp = productCreate_graphql($token, $shopurl, $input, $media);
    
    if (isset($resp['errors'])) {
        echo "GRAPHQL TOP-LEVEL HIBA: " . print_r($resp['errors'], true) . "<br>";
        continue;
    }
    
    if (!empty($resp['data']['productCreate']['userErrors'])) {
        echo "HIBA (userErrors): " . print_r($resp, true) . "<br>";
        continue;
    }
    
    if (!empty($resp['data']['productCreate']['product']['id'])) {
        $pid = $resp['data']['productCreate']['product']['id'];
        echo "TERMÉK LÉTREHOZVA: $pid (Handle: $handle)<br>";
    } else {
        echo "HIBA (nincs termék ID - teljes válasz): " . print_r($resp, true) . "<br>";
        continue;
    }
    // --- OPCIÓK HOZZÁADÁSA (új modell) ---
    if (!empty($productOptions)) {
        echo "Opciók hozzáadása...\n";
        $resp_options = productOptionsCreate_graphql($token, $shopurl, $pid, $productOptions);
        if (isset($resp_options['errors'])) {
            echo "GRAPHQL TOP-LEVEL HIBA (opciók): " . print_r($resp_options['errors'], true) . "<br>";
            continue;
        }
        if (!empty($resp_options['data']['productOptionsCreate']['userErrors'])) {
            echo "HIBA (opciók hozzáadása): " . print_r($resp_options, true) . "<br>";
            continue;
        } else {
            echo "OPCIÓK HOZZÁADVA (" . count($productOptions) . " opció)<br>";
        }
    }

    // --- VARIÁNSOK HOZZÁADÁSA ---
    echo "Variánsok hozzáadása...\n";
    $resp_var = productVariantsBulkCreate_graphql($token, $shopurl, $pid, $variants);
    if (isset($resp_var['errors'])) {
        echo "GRAPHQL TOP-LEVEL HIBA (variánsok): " . print_r($resp_var['errors'], true) . "<br>";
        continue;
    }
    if (!empty($resp_var['data']['productVariantsBulkCreate']['userErrors'])) {
        echo "HIBA (variánsok userErrors): " . print_r($resp_var, true) . "<br>";
        continue;
    } 
    if (!empty($resp_var['data']['productVariantsBulkCreate']['productVariants'])) {
        $created = $resp_var['data']['productVariantsBulkCreate']['productVariants'];
        echo "VARIÁNSOK LÉTREHOZVA (" . count($created) . " db)<br>";
    } else {
        echo "HIBA (variánsok: nincs productVariants - teljes válasz): " . print_r($resp_var, true) . "<br>";
        continue;
    }

    // --- KÉSZLET BEÁLLÍTÁS ---
    $qtySets = [];
    foreach ($created as $v) {
        $invId = $v['inventoryItem']['id'];
        $var_data = current(array_filter($variants, fn($var) => $var['sku'] === $v['sku']));
        if ($var_data['qty1'] > 0) $qtySets[] = ["inventoryItemId" => $invId, "locationId" => $loc1, "availableQuantity" => $var_data['qty1']];
        if ($var_data['qty2'] > 0) $qtySets[] = ["inventoryItemId" => $invId, "locationId" => $loc2, "availableQuantity" => $var_data['qty2']];
    }

    if ($qtySets) {
        echo "Készlet beállítás...\n";
        foreach (array_chunk($qtySets, 100) as $chunk) {
            $resp_qty = inventorySetQuantities_graphql($token, $shopurl, $chunk);
            if (!empty($resp_qty['data']['inventorySetQuantities']['userErrors'])) {
                echo "HIBA (készlet): " . print_r($resp_qty, true) . "<br>";
            } else {
                echo "KÉSZLET BEÁLLÍTVA (" . count($chunk) . " tétel)<br>";
            }
        }
    }

    // --- AKTIVÁLÁS ---
    echo "Termék aktiválás...\n";
    $resp_act = productActivate_graphql($token, $shopurl, $pid);
    if (!empty($resp_act['data']['productPublish']['product']['status'])) {  // Javítva productPublish-re
        echo "TERMÉK AKTIVÁLVA<br>";
    } else {
        echo "HIBA (aktiválás): " . print_r($resp_act, true) . "<br>";
    }

    // --- DB FRISSÍTÉS (GID-ek) ---
    foreach ($created as $cv) {
        $upd = $conn->prepare("UPDATE shopifyproducts SET shopifyproductid=?, shopifyvariantid=?, shopifyinventoryid=?, needs_update=0 WHERE generated_sku=?");
        $upd->bind_param("ssss", $pid, $cv['id'], $cv['inventoryItem']['id'], $cv['sku']);
        if ($upd->execute()) {
            echo "DB FRISSÍTVE: {$cv['sku']}<br>";
        } else {
            echo "DB HIBA: " . $upd->error . "<br>";
        }
        $upd->close();
    }

    $created_count++;
    echo "<b style='color:#0f0;background:#000;padding:8px'>TELJESEN KÉSZ – $created_count. termék</b><br>";

    // --- RATE LIMIT ---
    usleep(200000);  // 0.2 mp

    // --- IDŐKORLÁT ---
    if (time() - $start_time > $max_runtime) {
        echo "IDŐKORLÁT: 10 perc eltelt – $created_count termék létrehozva. Maradék a következő futásra.\n";
        break;
    }
}

echo "<h2>2. LÉPÉS KÉSZ – Összesen $created_count új termék</h2></pre>";
$conn->close();

// --- SEGÉDFÜGGVÉNY ---
function sanitize_handle($t) {
    return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($t ?: 'product')), '-') ?: 'product';
}
?>

