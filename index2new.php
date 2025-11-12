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

    // --- GYŰJTÉS (VARIÁNSOK, MÉDIA, OPCIÓK, ELSŐ ADATOK) ---
    $variants = $media = $productOptions = [];
    $first = null;
    $images = [];
    while ($row = $group->fetch_assoc()) {
        if (!$first) $first = $row;
        $variants[] = [
            'sku' => $row['generated_sku'],
            'barcode' => $row['variant_barcode'],
            'weight' => (float)$row['variant_grams'],
            'weightUnit' => 'GRAMS',
            'price' => (float)$row['price_huf'],
            'qty1' => (int)$row['qty_location_1'],
            'qty2' => (int)$row['qty_location_2']
        ];
        foreach (['image_src', 'image_src_2', 'image_src_3'] as $img_col) {
            if (!empty($row[$img_col]) && !in_array($row[$img_col], $images)) $images[] = $row[$img_col];
        }
        if (!empty($row['option1_name']) && !in_array($row['option1_name'], array_column($productOptions, 'name'))) {
            $productOptions[] = ['name' => $row['option1_name'], 'values' => []];  // Values added later if needed
        }
        // ... (continue with option2 if present)
    }

    // --- DUPLIKÁTUM ELLENŐRZÉS SHOPIFY-BEN (SKU ALAPJÁN) ---
    echo "Duplikátum ellenőrzés Shopify-ben...\n";
    $existing_product_gid = null;
    $first_variant_sku = $variants[0]['sku'];  // Ellenőrizzük az első variáns SKU-ját (generated_sku)
    $existing = productQueryBySku_graphql($token, $shopurl, $first_variant_sku);

    if ($existing) {
        $existing_product_gid = $existing['product_gid'];
        echo "MÁR LÉTEZIK Shopify-ben: Termék GID - $existing_product_gid (SKU: $first_variant_sku alapján)\n";

        // --- ÖSSZES VARIÁNS LEKÉRDEZÉSE A TERMÉKBŐL ÉS EGYEZTETÉS ---
        $resp_variants = getProductVariants_graphql($token, $shopurl, $existing_product_gid);
        if (empty($resp_variants['data']['product']['variants']['nodes'])) {
            echo "HIBA: Nem sikerült lekérdezni a variánsokat - $existing_product_gid\n";
            continue;
        }

        $shopify_variants = $resp_variants['data']['product']['variants']['nodes'];
        $matched_count = 0;

        foreach ($variants as $db_variant) {
            $matched = false;
            foreach ($shopify_variants as $shop_variant) {
                if ($shop_variant['sku'] === $db_variant['sku']) {
                    // --- DB FRISSÍTÉS (GID-ek) ---
                    $upd = $conn->prepare("UPDATE shopifyproducts SET shopifyproductid=?, shopifyvariantid=?, shopifyinventoryid=?, needs_update=0 WHERE generated_sku=?");
                    $upd->bind_param("ssss", $existing_product_gid, $shop_variant['id'], $shop_variant['inventoryItem']['id'], $db_variant['sku']);
                    if ($upd->execute()) {
                        echo "DB FRISSÍTVE (meglévő): {$db_variant['sku']}\n";
                        $matched_count++;
                    } else {
                        echo "DB HIBA: " . $upd->error . "\n";
                    }
                    $matched = true;
                    break;
                }
            }
            if (!$matched) {
                echo "FIGYELEM: Nem talált egyező variáns Shopify-ben: {$db_variant['sku']} - manuális ellenőrzés javasolt!\n";
            }
        }

        if ($matched_count === count($variants)) {
            echo "TELJES EGYEZÉS: $matched_count variáns - kihagyjuk a létrehozást.\n";
            $created_count++;  // Számoljuk, de nem új
            continue;  // Ugrás a következő csoportra
        } else {
            echo "RÉSZLEGES EGYEZÉS: Csak $matched_count / " . count($variants) . " - folytatjuk létrehozással (de manuálisan ellenőrizd)!\n";
            // Itt dönthetsz: continue vagy proceed to create (de duplicate kockázata)
        }
    } else {
        echo "Nincs duplikátum - folytatjuk létrehozással.\n";
    }

    // --- TERMÉK BEMENET (HANDLE GENERÁLÁS, STB.) ---
    $input = [
        'title' => $first['title'] ?? '',
        'descriptionHtml' => $first['body_html'] ?? '',
        'vendor' => $first['vendor'] ?? '',
        'productType' => $first['type'] ?? '',
        'tags' => explode(',', $first['tags'] ?? ''),
        'status' => 'DRAFT'
        // NINCS 'handle' => ... – kihagyjuk!
    ];

    $media = array_map(fn($url) => ['originalSource' => $url, 'mediaContentType' => 'IMAGE'], $images);

    echo "Termék létrehozása (handle auto-generált a Shopify által)...<br>";
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
        $generated_handle = $resp['data']['productCreate']['product']['handle'] ?? 'unknown';
        echo "TERMÉK LÉTREHOZVA: $pid (Handle: $generated_handle)<br>";
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
