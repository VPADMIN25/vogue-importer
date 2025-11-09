<?php
// indexnew.php – V24 – VÉGLEGES (A "HIBÁS NAGYBETŰS ÖRÖKBEFOGADÁS" JAVÍTÁSA)
ini_set('max_execution_time', 1800);  // 30 perc
set_time_limit(1800);
ini_set('memory_limit', '2G');

echo "<pre style='font-family:Consolas;font-size:14px'>";
echo "<h2>1. LÉPÉS – BEOLVASÁS + SZINKRONIZÁLÁS (V24 - "Trilingual" Örökbefogadás)</h2>";

// --- KAPCSOLAT (RETRY) ---
$env = [
    'DB_HOST' => getenv('DB_HOST'),
    'DB_USER' => getenv('DB_USER'),
    'DB_PASS' => getenv('DB_PASS'),
    'DB_NAME' => getenv('DB_NAME'),
    'DB_PORT' => (int)getenv('DB_PORT'),
    'DB_SSLMODE' => getenv('DB_SSLMODE')
];
// ... (A kapcsolatkódod változatlan marad) ...
$conn = null;
$maxRetries = 15;
$connectTimeout = 30;
for ($i = 0; $i < $maxRetries; $i++) {
    $conn = mysqli_init();
    if ($env['DB_SSLMODE'] === 'require') {
        mysqli_options($conn, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);
    }
    mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, $connectTimeout);
    if (@mysqli_real_connect($conn, $env['DB_HOST'], $env['DB_USER'], $env['DB_PASS'], $env['DB_NAME'], $env['DB_PORT'], NULL, MYSQLI_CLIENT_SSL)) {
        echo "Kapcsolódva: {$env['DB_HOST']}:{$env['DB_PORT']} (próbálkozás: " . ($i + 1) . ")\n";
        break;
    }
    $error = mysqli_connect_error();
    echo "Kapcsolódás sikertelen (próbálkozás: " . ($i + 1) . "/$maxRetries): $error\n";
    if ($i < $maxRetries - 1) sleep(15);
}
if (!$conn) die("FATAL: Nem sikerült kapcsolódni a MySQL-hez 15 próbálkozás után!");
mysqli_set_charset($conn, "utf8mb4");
echo "Kapcsolódva: {$env['DB_HOST']}\n";

require_once("helpers/shopifyGraphQL.php");
require_once("helpers/general.php");
$shopurl = getenv('SHOPIFY_SHOP_URL');
$token = getenv('SHOPIFY_API_TOKEN');
if (empty($shopurl) || empty($token)) die("Hiányzó SHOPIFY változók.");

$run_timestamp = date('Y-m-d H:i:s');
$total_rows = $total_created = $total_updated = $total_adopted = 0;

$feeds = [
    ['url' => 'https://voguepremiere-csv-storage.fra1.digitaloceanspaces.com/stockfirmati_final_feed_huf.csv', 'location' => 1, 'qty_col' => 'Stockfirmati Raktár Inventory Qty'],
    ['url' => 'https://voguepremiere-csv-storage.fra1.digitaloceanspaces.com/peppela_final_feed_huf.csv', 'location' => 2, 'qty_col' => 'Peppela Inventory Qty'],
];

// Két SKU-t keresünk (a régit ÉS az újat)
$stmt_check = $conn->prepare("SELECT id, needs_update, qty_location_1, qty_location_2, price_huf FROM shopifyproducts WHERE generated_sku = ? OR variant_sku = ?");
$stmt_update = $conn->prepare("UPDATE shopifyproducts SET price_huf=?, qty_location_1=?, qty_location_2=?, needs_update=?, last_seen_in_feed=?, variant_sku=?, generated_sku=?, handle=?, title=?, body=?, vendor=?, type=?, tags=?, barcode=?, grams=?, img_src=?, img_src_2=?, img_src_3=?, option1_name=?, option1_value=?, option2_name=?, option2_value=? WHERE id=?");
$stmt_insert = $conn->prepare("INSERT INTO shopifyproducts (generated_sku, variant_sku, handle, title, body, vendor, type, tags, price_huf, qty_location_1, qty_location_2, barcode, grams, img_src, img_src_2, img_src_3, option1_name, option1_value, option2_name, option2_value, needs_update, last_seen_in_feed, shopifyproductid, shopifyvariantid, shopifyinventoryid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

foreach ($feeds as $feed) {
    echo "<hr><h3>Feed: {$feed['url']}</h3>";
    // ... (A feed letöltés kódja változatlan marad) ...
    $content = @file_get_contents($feed['url']);
    if (!$content) { echo "Hiba a feed letöltésekor!\n"; continue; }
    $temp = fopen('php://memory', 'r+');
    fwrite($temp, $content); rewind($temp);
    $headers = fgetcsv($temp, 0, ",", "\"", "\\");
    $map = array_flip(array_map('strtolower', $headers));
    $required = ['handle', 'vendor', 'variant sku', 'variant price', strtolower($feed['qty_col'])];
    foreach ($required as $r) if (!isset($map[$r])) { echo "Hiányzó oszlop: $r\n"; continue 2; }

    $chunk_size = 1000;
    $counter = 0;
    while (($data = fgetcsv($temp, 0, ",", "\"", "\\")) !== FALSE) {
        $counter++;
        $total_rows++;

        $variantSkuGroup = trim($data[$map['variant sku']]);
        if (empty($variantSkuGroup)) { echo "ÜRES SKU → átugorva (sor: $total_rows)\n"; continue; }

        $option1Val = trim($data[$map['option1 value'] ?? ''] ?? '');
        $option2Val = trim($data[$map['option2 value'] ?? ''] ?? '');
        
        // --- A 3 KULCS GENERÁLÁSA ---
        // 1. A HELYES, KISBETŰS KULCS (A JÖVŐ)
        $generated_sku = $variantSkuGroup;
        if ($option1Val) $generated_sku .= "-" . preg_replace('/[^a-z0-9]+/', '-', strtolower($option1Val));
        if ($option2Val) $generated_sku .= "-" . preg_replace('/[^a-z0-9]+/', '-', strtolower($option2Val));
        
        // 2. A RÉGI, NAGYBETŰS SYNCX KULCS (A TE ESETED)
        $old_syncx_sku = $variantSkuGroup;
        if ($option1Val) $old_syncx_sku .= "-" . $option1Val;
        if ($option2Val) $old_syncx_sku .= "-" . $option2Val;
        
        // 3. A RÉGI, CSOPORT KULCS (A "Norway 1963" esete)
        // (Ez maga a $variantSkuGroup)

        $csv_price = (float)($data[$map['variant price']] ?? 0);
        $final_price = $csv_price; 
        
        $qty1 = ($feed['location'] == 1) ? (int)$data[$map[strtolower($feed['qty_col'])]] : 0;
        $qty2 = ($feed['location'] == 2) ? (int)$data[strtolower($feed['qty_col'])] : 0;

        // A HIBÁS ADAT KEZELÉSE
        $stmt_check->bind_param("ss", $generated_sku, $variantSkuGroup);
        $stmt_check->execute();
        $res = $stmt_check->get_result();

        $needs_update = 0; $gid_product = $gid_variant = $gid_inventory = null;

        if ($res->num_rows > 0) {
            // === ESET 1: TERMÉK MÁR ISMERJÜK (GYORS) ===
            $row = $res->fetch_assoc();
            
            if ($feed['location'] == 1) { $qty2 = $row['qty_location_2']; } else { $qty1 = $row['qty_location_1']; }

            if ($row['price_huf'] != $final_price || $row['qty_location_1'] != $qty1 || $row['qty_location_2'] != $qty2 || $row['needs_update'] == 20) {
                $needs_update = 10;
            } else {
                $needs_update = $row['needs_update'];
            }
            
            $stmt_update->bind_param("diiissssssssssssssssssi",
                $final_price, $qty1, $qty2, $needs_update, $run_timestamp,
                $variantSkuGroup, $generated_sku, 
                $data[$map['handle']], $data[$map['vendor']], $data[$map['body (html)'] ?? ''],
                $data[$map['vendor']], $data[$map['type']], $data[$map['tags']],
                $data[$map['variant barcode'] ?? ''], $data[$map['variant grams'] ?? ''],
                $data[$map['image src'] ?? ''], $data[$map['image src 2'] ?? ''], $data[$map['image src 3'] ?? ''],
                $data[$map['option1 name'] ?? ''], $option1Val, $data[$map['option2 name'] ?? ''], $option2Val,
                $row['id']
            );
            $stmt_update->execute();
            $total_updated++;

        } else {
            // === ESET 2: TERMÉK ÚJ NEKÜNK (A TE ESETED) ===
            
            // 1. Próba: Helyes, kisbetűs SKU
            $shopifyGids = productQueryBySku_graphql($token, $shopurl, $generated_sku);

            // 2. Próba: Régi, nagybetűs SKU (A TE CHC...-M-BROWN ESETED)
            if ($shopifyGids === null) {
                $shopifyGids = productQueryBySku_graphql($token, $shopurl, $old_syncx_sku);
            }
            
            // 3. Próba: Régi, csoport SKU (A "Norway 1963" esete)
            if ($shopifyGids === null) {
                $shopifyGids = productQueryBySku_graphql($token, $shopurl, $variantSkuGroup);
            }

            if ($shopifyGids === null) {
                // VADONATÚJ TERMÉK (Sehol nincs)
                $needs_update = 2; // 2 = Létrehozás
                $total_created++;
            } else {
                // ÖRÖKBEFOGADOTT TERMÉK (Megtaláltuk a Shopify-ban)
                $needs_update = 10; // 10 = Frissítésre és "Öngyógyításra" vár
                $gid_product = $shopifyGids['product_gid'];
                $gid_variant = $shopifyGids['variant_gid'];
                $gid_inventory = $shopifyGids['inventory_gid'];
                $total_adopted++;
            }
            
            $stmt_insert->bind_param("sssssssssiissssssssssssss",
                $generated_sku, $variantSkuGroup, $data[$map['handle']], $data[$map['vendor']], $data[$map['body (html)'] ?? ''],
                $data[$map['vendor']], $data[$map['type']], $data[$map['tags']],
                $final_price, $qty1, $qty2,
                $data[$map['variant barcode'] ?? ''], $data[$map['variant grams'] ?? ''],
                $data[$map['image src'] ?? ''], $data[$map['image src 2'] ?? ''], $data[$map['image src 3'] ?? ''],
                $data[$map['option1 name'] ?? ''], $option1Val, $data[$map['option2 name'] ?? ''], $option2Val,
                $needs_update, $run_timestamp, $gid_product, $gid_variant, $gid_inventory
            );
            $stmt_insert->execute();
        }

        if ($counter % $chunk_size === 0) {
            echo "Feldolgozva: $counter sor\n";
            gc_collect_cycles();
        }
    }
    fclose($temp);
}

// --- ARCHIVÁLÁS (MOST MÁR HELYESEN FOG MŰKÖDNI) ---
$archive_sql = "UPDATE shopifyproducts SET needs_update = 20 WHERE last_seen_in_feed < ? AND needs_update NOT IN (2, 10)";
$stmt_archive = $conn->prepare($archive_sql);
$stmt_archive->bind_param("s", $run_timestamp);
$stmt_archive->execute();
echo "Archiválva (Törlésre előkészítve): " . $stmt_archive->affected_rows . " variáns\n";

echo "<hr><b>Feldolgozva: $total_rows | Új: $total_created | Frissítve: $total_updated | Átvéve: $total_adopted</b>\n";
echo "<h2>1. LÉPÉS KÉSZ</h2></pre>";
$conn->close();
?>
