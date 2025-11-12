<?php
// indexnew.php – V26 – TELJES, JAVÍTOTT, HOSSZÚ VERZIÓ
ini_set('max_execution_time', 1800);  // 30 perc
set_time_limit(1800);
ini_set('memory_limit', '2G');

echo "<pre style='font-family:Consolas;font-size:14px'>";
echo "<h2>1. LÉPÉS – BEOLVASÁS + SZINKRONIZÁLÁS (TELJES VERZIÓ)</h2>";

// ========================================
// 1. KAPCSOLAT (RETRY + LOG)
// ========================================
$env = [
    'DB_HOST' => getenv('DB_HOST'),
    'DB_USER' => getenv('DB_USER'),
    'DB_PASS' => getenv('DB_PASS'),
    'DB_NAME' => getenv('DB_NAME'),
    'DB_PORT' => (int)getenv('DB_PORT'),
    'DB_SSLMODE' => getenv('DB_SSLMODE')
];

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
echo "Karakterkódolás: utf8mb4\n";

// ========================================
// 2. SHOPIFY BEÁLLÍTÁSOK
// ========================================
require_once("helpers/shopifyGraphQL.php");
require_once("helpers/general.php");

$shopurl = getenv('SHOPIFY_SHOP_URL');
$token = getenv('SHOPIFY_API_TOKEN');
if (empty($shopurl) || empty($token)) die("Hiányzó SHOPIFY változók.");

$run_timestamp = date('Y-m-d H:i:s');
echo "Futás időbélyege: $run_timestamp\n";

$total_rows = $total_created = $total_updated = 0;

// ========================================
// 3. FEED-ek (TESZT: CSAK 1, ÉLES: MINDKETTŐ)
// ========================================
$feeds = [
    ['url' => 'https://voguepremiere-csv-storage.fra1.digitaloceanspaces.com/stockfirmati_final_feed_huf.csv', 'location' => 1, 'qty_col' => 'Stockfirmati Raktár Inventory Qty'],
    // ['url' => 'https://voguepremiere-csv-storage.fra1.digitaloceanspaces.com/peppela_final_feed_huf.csv', 'location' => 2, 'qty_col' => 'Peppela Inventory Qty'],
];

$stmt_check = $conn->prepare("SELECT id, needs_update, qty_location_1, qty_location_2, price_huf FROM shopifyproducts WHERE generated_sku = ?");
$stmt_update = $conn->prepare("UPDATE shopifyproducts SET price_huf=?, qty_location_1=?, qty_location_2=?, needs_update=?, last_seen_in_feed=?, variant_sku=?, generated_sku=?, handle=?, title=?, body=?, vendor=?, type=?, tags=?, barcode=?, grams=?, img_src=?, img_src_2=?, img_src_3=?, option1_name=?, option1_value=?, option2_name=?, option2_value=? WHERE id=?");
$stmt_insert = $conn->prepare("INSERT INTO shopifyproducts (generated_sku, variant_sku, handle, title, body, vendor, type, tags, price_huf, qty_location_1, qty_location_2, barcode, grams, img_src, img_src_2, img_src_3, option1_name, option1_value, option2_name, option2_value, needs_update, last_seen_in_feed) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$counter = 0;
$max_test_rows = 5; // TESZT MÓD – ÉLESBEN: TÖRLÉSD!

foreach ($feeds as $feed) {
    echo "<hr><h3>FEED: {$feed['url']} (Location: {$feed['location']})</h3>";
    
    $content = @file_get_contents($feed['url']);
    if (!$content) { 
        echo "HIBA: Feed letöltése sikertelen!\n"; 
        continue; 
    }
    $temp = fopen('php://memory', 'r+');
    fwrite($temp, $content); 
    rewind($temp);

    $headers = fgetcsv($temp, 0, ",", "\"", "\\");
    if (!$headers) {
        echo "HIBA: Üres vagy hibás CSV fejléc!\n";
        fclose($temp);
        continue;
    }

    $map = array_flip(array_map('strtolower', $headers));
    echo "Oszlopok száma: " . count($map) . "\n";

    $required = ['handle', 'vendor', 'variant sku', 'variant price', strtolower($feed['qty_col'])];
    foreach ($required as $r) {
        if (!isset($map[$r])) { 
            echo "HIÁNYZÓ Oszlop: $r\n"; 
            continue 2; 
        }
    }

    $chunk_size = 1000;
    while (($data = fgetcsv($temp, 0, ",", "\"", "\\")) !== FALSE) {
        if ($max_test_rows > 0 && $counter >= $max_test_rows) break;
        $counter++;
        $total_rows++;

        // --- SKU ÉS GENERÁLT SKU ---
        $variantSkuGroup = strtolower(trim($data[$map['variant sku']]));
        if (empty($variantSkuGroup)) { 
            echo "ÜRES SKU → átugorva (sor: $total_rows)\n"; 
            continue; 
        }

        $option1Val = trim($data[$map['option1 value'] ?? ''] ?? '');
        $option2Val = trim($data[$map['option2 value'] ?? ''] ?? '');
        
        $generated_sku = $variantSkuGroup;
        if ($option1Val) $generated_sku .= "-" . preg_replace('/[^a-z0-9]+/', '-', strtolower($option1Val));
        if ($option2Val) $generated_sku .= "-" . preg_replace('/[^a-z0-9]+/', '-', strtolower($option2Val));
        
        $csv_price = (float)($data[$map['variant price']] ?? 0);
        $final_price = $csv_price;

        $qty1 = ($feed['location'] == 1) ? (int)$data[$map[strtolower($feed['qty_col'])]] : 0;
        $qty2 = ($feed['location'] == 2) ? (int)$data[$map[strtolower($feed['qty_col'])]] : 0;

        echo "SOR $total_rows | SKU: $variantSkuGroup | Generated: $generated_sku | Ár: $final_price | Qty1: $qty1 | Qty2: $qty2\n";

        // --- DB ELLENŐRZÉS ---
        $stmt_check->bind_param("s", $generated_sku);
        $stmt_check->execute();
        $res = $stmt_check->get_result();

        if ($res->num_rows > 0) {
            // --- MÁR LÉTEZIK → FRISSÍTÉS ---
            $row = $res->fetch_assoc();
            
            if ($feed['location'] == 1) { $qty2 = $row['qty_location_2']; } 
            else { $qty1 = $row['qty_location_1']; }

            $needs_update = ($row['price_huf'] != $final_price || $row['qty_location_1'] != $qty1 || $row['qty_location_2'] != $qty2 || $row['needs_update'] == 20) ? 10 : $row['needs_update'];
            
            $stmt_update->bind_param("diiisssssssssssssssssii",
                $final_price, $qty1, $qty2, $needs_update, $run_timestamp,
                $variantSkuGroup, $generated_sku, 
                $data[$map['handle']], $data[$map['title']], $data[$map['body (html)'] ?? ''],
                $data[$map['vendor']], $data[$map['type']], $data[$map['tags']],
                $data[$map['variant barcode'] ?? ''], $data[$map['variant grams'] ?? ''],
                $data[$map['image src'] ?? ''], $data[$map['image src 2'] ?? ''], $data[$map['image src 3'] ?? ''],
                $data[$map['option1 name'] ?? ''], $option1Val, $data[$map['option2 name'] ?? ''], $option2Val,
                $row['id']
            );
            if ($stmt_update->execute()) {
                $total_updated++;
                echo "FRISSÍTVE (DB): $generated_sku\n";
            } else {
                echo "HIBA (UPDATE): " . $stmt_update->error . "\n";
            }

        } else {
            // --- ÚJ TERMÉK → INSERT ---
            $needs_update = 2;
            $total_created++;
            
            $stmt_insert->bind_param("sssssssssiisssssssssss",
                $generated_sku, $variantSkuGroup, $data[$map['handle']], $data[$map['title']], $data[$map['body (html)'] ?? ''],
                $data[$map['vendor']], $data[$map['type']], $data[$map['tags']],
                $final_price, $qty1, $qty2,
                $data[$map['variant barcode'] ?? ''], $data[$map['variant grams'] ?? ''],
                $data[$map['image src'] ?? ''], $data[$map['image src 2'] ?? ''], $data[$map['image src 3'] ?? ''],
                $data[$map['option1 name'] ?? ''], $option1Val, $data[$map['option2 name'] ?? ''], $option2Val,
                $needs_update, $run_timestamp
            );
            if ($stmt_insert->execute()) {
                echo "LÉTREHOZVA (DB): $generated_sku\n";
            } else {
                echo "HIBA (INSERT): " . $stmt_insert->error . "\n";
            }
        }

        // --- CHUNK LOG ---
        if ($counter % $chunk_size === 0) {
            echo "Feldolgozva: $counter sor (memória: " . memory_get_usage(true)/1024/1024 . " MB)\n";
            gc_collect_cycles();
        }
    }
    fclose($temp);
    echo "Feed feldolgozva: " . ($counter - ($counter % $chunk_size)) . " + " . ($counter % $chunk_size) . " sor\n";
}

// ========================================
// 4. ARCHIVÁLÁS (TÖRLÉSRE JELÖLÉS)
// ========================================
$archive_sql = "UPDATE shopifyproducts SET needs_update = 20 WHERE last_seen_in_feed < ? AND needs_update NOT IN (2, 10)";
$stmt_archive = $conn->prepare($archive_sql);
$stmt_archive->bind_param("s", $run_timestamp);
$stmt_archive->execute();
$archived = $stmt_archive->affected_rows;
echo "ARCHIVÁLVA (törlésre jelölve): $archived variáns\n";

// ========================================
// 5. ÖSSZESÍTÉS
// ========================================
echo "<hr><b>Feldolgozva: $total_rows | Új (létrehozásra vár): $total_created | Frissítve: $total_updated | Archiválva: $archived</b>\n";
echo "<h2>1. LÉPÉS KÉSZ – ADATBÁZIS SZINKRONIZÁLVA</h2></pre>";
$conn->close();
?>

