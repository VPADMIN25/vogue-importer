<?php
// index2new.php – VÉGLEGES JAVÍTOTT VERZIÓ

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
if (empty($shopurl) || empty($token)) die("Hiányzó SHOPIFY változók.");

$loc1 = getShopifyLocationGid($token, $shopurl, "Italy Vogue Premiere Warehouse 1");
$loc2 = getShopifyLocationGid($token, $shopurl, "Italy Vogue Premiere Warehouse 2");
if (!$loc1 || !$loc2) die("Raktárak hiányoznak!");

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
    $skuGroup = $g['variant_sku'];
    echo "\n<hr><b>TERMÉKCSOPORT: $skuGroup</b><br>";

    $stmt = $conn->prepare("SELECT * FROM shopifyproducts WHERE variant_sku=? AND needs_update=2");
    $stmt->bind_param("s", $skuGroup);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows == 0) {
        echo "Nincs variáns ehhez a SKU-hoz!\n";
        $stmt->close();
        continue;
    }

    $titleRow = $res->fetch_assoc();
    $title = trim($titleRow['vendor'] ?? '');
    if (!$title) {
        echo "ÜRES CÍM – átugorva!\n";
        $stmt->close();
        continue;
    }

    // --- ADATGYŰJTÉS (MINDEN VARIÁNS) ---
    $res->data_seek(0);
    $images = []; $variants = []; $options = [];
    while ($r = $res->fetch_assoc()) {
        // KÉPEK
        foreach (['img_src', 'img_src_2', 'img_src_3'] as $k) {
            if (!empty($r[$k])) {
                $images[] = ["originalSource" => $r[$k], "mediaContentType" => "IMAGE"];
            }
        }
        // OPCIÓK NEVEI
        if (!empty($r['option1_value'])) $options[] = $r['option1_name'];
        if (!empty($r['option2_value'])) $options[] = $r['option2_name'];

        // VARIÁNS ADATOK
        $variants[] = [
            "sku" => $r['generated_sku'] ?? '',
            "price" => number_format((float)($r['price_huf'] ?? 0), 2, '.', ''),
            "inventoryPolicy" => "DENY",
            "requiresShipping" => true,
            "inventoryManagement" => "SHOPIFY",
            "option1" => !empty($r['option1_value']) ? $r['option1_value'] : null,
            "option2" => !empty($r['option2_value']) ? $r['option2_value'] : null,
            "barcode" => !empty($r['barcode']) ? $r['barcode'] : null,
            "weight" => (float)($r['grams'] / 1000 ?? 0),
            "weightUnit" => "KILOGRAMS",
            "qty1" => (int)($r['qty_location_1'] ?? 0),
            "qty2" => (int)($r['qty_location_2'] ?? 0),
        ];
    }
    $stmt->close();

    $images = array_values(array_unique($images, SORT_REGULAR));
    $options = array_values(array_unique(array_filter($options)));
    $tags = array_filter(array_map('trim', explode(',', $titleRow['tags'] ?? '')));
    
    // ÁTALAKÍTÁS a 2024-04 API-hoz ( productOptions mező )
    $productOptions = [];
    foreach ($options as $optName) {
        $productOptions[] = ['name' => $optName];
    }

    echo "Variánsok száma: " . count($variants) . "<br>";
    echo "Képek száma: " . count($images) . "<br>";
    echo "Opciók: " . implode(', ', $options) . "<br>";

    // --- JAVÍTOTT TERMÉK LÉTREHOZÁS (OPTIMALIZÁLT HANDLE-KEZELÉSSEL) ---
    
    $base_handle = sanitize_handle($titleRow['handle'] ?: $skuGroup);
    $product_created = false;
    $pid = null; // Termék GID
    $num = null; // Termék numerikus ID (admin linkhez)

    for ($i = 0; $i <= 50; $i++) { // Max 51 próbálkozás (alap, -1, -2, ... -50)
        $current_handle = ($i == 0) ? $base_handle : "$base_handle-$i";
        
        if ($i > 0) {
            echo "Handle ($base_handle) foglalt, új próba: <b>$current_handle</b><br>";
        } else {
            echo "HANDLE: <b>$current_handle</b><br>";
        }

        // 1. $input tömb összerakása az *aktuális* handle-lel
        $input = [
            "title" => $title,
            "handle" => $current_handle, // Aktuális handle használata
            "descriptionHtml" => $titleRow['body'] ?? '',
            "vendor" => $titleRow['vendor'] ?? 'Unknown',
            "productType" => $titleRow['type'] ?? 'Clothing',
            "tags" => $tags,
            "status" => "DRAFT"
            // Az 'options' kulcsot szándékosan töröltük (2024-04 API)
        ];
    
        // 2. A fő productCreate hívás (média és opciók átadásával)
        $resp = productCreate_graphql($token, $shopurl, $input, $images, $productOptions); 

        // 3. Siker ellenőrzése
        if (!empty($resp['data']['productCreate']['product']['id'])) {
            $pid = $resp['data']['productCreate']['product']['id'];
            $num = substr($pid, strrpos($pid, '/') + 1);
            echo "LÉTREHOZVA → <a href='https://$shopurl/admin/products/$num' target='_blank'>$current_handle</a><br>";
            $product_created = true;
            break; // Siker, kilépünk a handle-próbálkozó ciklusból
        }

        // 4. Hibaellenőrzés: "handle taken" hiba?
        $is_handle_error = false;
        if (!empty($resp['data']['productCreate']['userErrors'])) {
            foreach ($resp['data']['productCreate']['userErrors'] as $err) {
                // Ellenőrizzük, hogy a hiba a 'handle' mezőre vonatkozik-e
                if (isset($err['field']) && in_array('handle', $err['field'])) {
                    $is_handle_error = true;
                    break;
                }
            }
        } elseif (!empty($resp['errors'])) {
             // Általános GraphQL hiba (pl. rate limit)
             echo "GraphQL Hiba: " . ($resp['errors'][0]['message'] ?? 'Ismeretlen hiba') . "<br>";
        }

        if ($is_handle_error) {
            // Ez egy "handle foglalt" hiba. A ciklus folytatódik (-1, -2, stb.)
            usleep(500000); // 0.5mp várakozás
            continue;
        } else {
            // Ez egy VÉGZETES hiba (nem handle hiba, pl. schema, vagy rate limit)
            echo "HIBA: termék létrehozása sikertelen!<br>";
            echo "<pre>" . print_r($resp, true) . "</pre>";
            break; // Kilépünk a handle-próbálkozó ciklusból (de $product_created false marad)
        }
    } // -- Handle-próbálkozó ciklus vége

    // 5. Végső ellenőrzés
    if (!$product_created) {
        echo "Nem sikerült a termék létrehozása 51 próbálkozás után. Átugrás a következő csoportra.<br>";
        continue; // Ugrás a fő 'while ($g = $groups->fetch_assoc())' ciklus következő elemére
    }
    
    // --- OPCIÓK HOZZÁADÁSA (Külön lépésben) ---
    /*
    if ($options) {
        ... (Ezt a blokkot helyesen hagytad kikommentelve) ...
    }
    */
    
    // --- VARIÁNSOK TÖMEGES LÉTREHOZÁSA (JAVÍTVA) ---
    $varInputs = array_map(fn($v) => [
        'variantInput' => array_filter([
            "sku" => $v['sku'], "price" => $v['price'], "inventoryPolicy" => $v['inventoryPolicy'],
            "requiresShipping" => $v['requiresShipping'],
            "inventoryManagement" => $v['inventoryManagement'], // Javított kulcs
            "option1" => $v['option1'], "option2" => $v['option2'], "barcode" => $v['barcode'],
            "weight" => $v['weight'], "weightUnit" => $v['weightUnit']
        ], fn($val) => $val !== null)
    ], $variants);

    $resp_vars = productVariantsBulkCreate_graphql($token, $shopurl, $pid, $varInputs);
    $created = $resp_vars['data']['productVariantsBulkCreate']['productVariants'] ?? [];

    if (empty($created)) {
        echo "HIBA: variánsok létrehozása sikertelen!<br>";
        echo "<pre>" . print_r($resp_vars, true) . "</pre>";
        continue;
    }
    echo "VARIÁNSOK LÉTREHOZVA: " . count($created) . "<br>";

    // --- KÉSZLET BEÁLLÍTÁSA ---
    // (A felesleges SÚLY beállítást töröltük, mivel a bulkCreate már kezeli)
    $qtySets = [];
    foreach ($created as $i => $cv) {
        $invId = $cv['inventoryItem']['id'];
        
        if (!isset($variants[$i])) {
             echo "HIBA: Indexelési probléma a variánsoknál ($i).<br>";
             continue;
        }
        $v = $variants[$i];

        // Készlet
        if ($v['qty1'] > 0) $qtySets[] = ["inventoryItemId" => $invId, "locationId" => $loc1, "availableQuantity" => $v['qty1']];
        if ($v['qty2'] > 0) $qtySets[] = ["inventoryItemId" => $invId, "locationId" => $loc2, "availableQuantity" => $v['qty2']];
    }

    if ($qtySets) {
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
    $resp_act = productActivate_graphql($token, $shopurl, $pid);
    if (!empty($resp_act['data']['productUpdate']['product']['status'])) {
        echo "TERMÉK AKTIVÁLVA<br>";
    }

    // --- DB FRISSÍTÉS (GID-ek) ---
    foreach ($created as $cv) {
        $upd = $conn->prepare("UPDATE shopifyproducts SET shopifyproductid=?, shopifyvariantid=?, shopifyinventoryid=?, needs_update=0 WHERE generated_sku=?");
        $upd->bind_param("ssss", $pid, $cv['id'], $cv['inventoryItem']['id'], $cv['sku']);
        if ($upd->execute()) {
            echo "DB FRISSÍTVE: {$cv['sku']}<br>";
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
// --- EDDIG TART A CSERE ---

echo "<h2>2. LÉPÉS KÉSZ – Összesen $created_count új termék</h2></pre>";
$conn->close();

// --- SEGÉDFÜGGVÉNY ---
function sanitize_handle($t) {
    return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($t ?: 'product')), '-') ?: 'product';
}
?>










