<?php
// indexnew.php – V26 – TESZT MÓD (1 TERMÉK)
ini_set('max_execution_time', 1800);
set_time_limit(1800);
ini_set('memory_limit', '2G');

echo "<pre style='font-family:Consolas;font-size:14px'>";
echo "<h2>1. LÉPÉS – BEOLVASÁS + SZINKRONIZÁLÁS (TESZT: 1 TERMÉK)</h2>";

// --- KAPCSOLAT ---
$env = [
    'DB_HOST' => getenv('DB_HOST'),
    'DB_USER' => getenv('DB_USER'),
    'DB_PASS' => getenv('DB_PASS'),
    'DB_NAME' => getenv('DB_NAME'),
    'DB_PORT' => (int)getenv('DB_PORT'),
    'DB_SSLMODE' => getenv('DB_SSLMODE')
];

$conn = null;
$maxRetries = 5;
for ($i = 0; $i < $maxRetries; $i++) {
    $conn = mysqli_init();
    if ($env['DB_SSLMODE'] === 'require') {
        mysqli_options($conn, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);
    }
    mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 10);
    
    if (@mysqli_real_connect($conn, $env['DB_HOST'], $env['DB_USER'], $env['DB_PASS'], $env['DB_NAME'], $env['DB_PORT'], NULL, MYSQLI_CLIENT_SSL)) {
        echo "Kapcsolódva: {$env['DB_HOST']}:{$env['DB_PORT']}\n";
        break;
    }
    echo "Kapcsolódás sikertelen... próbálkozás " . ($i + 1) . "\n";
    sleep(5);
}
if (!$conn) die("FATAL: Nem sikerült kapcsolódni a MySQL-hez!");

mysqli_set_charset($conn, "utf8mb4");

require_once("helpers/shopifyGraphQL.php");
require_once("helpers/general.php");

$shopurl = getenv('SHOPIFY_SHOP_URL');
$token = getenv('SHOPIFY_API_TOKEN');
if (empty($shopurl) || empty($token)) die("Hiányzó SHOPIFY változók.");

$run_timestamp = date('Y-m-d H:i:s');
$total_rows = $total_created = $total_updated = 0;

// TESZT: CSAK 1 TERMÉK (pl. első 5 sor)
$feeds = [
    ['url' => 'https://voguepremiere-csv-storage.fra1.digitaloceanspaces.com/stockfirmati_final_feed_huf.csv', 'location' => 1, 'qty_col' => 'Stockfirmati Raktár Inventory Qty'],
    // ['url' => 'https://voguepremiere-csv-storage.fra1.digitaloceanspaces.com/peppela_final_feed_huf.csv', 'location' => 2, 'qty_col' => 'Peppela Inventory Qty'],
];

$stmt_check = $conn->prepare("SELECT id, needs_update, qty_location_1, qty_location_2, price_huf FROM shopifyproducts WHERE generated_sku = ?");
$stmt_update = $conn->prepare("UPDATE shopifyproducts SET price_huf=?, qty_location_1=?, qty_location_2=?, needs_update=?, last_seen_in_feed=?, variant_sku=?, generated_sku=?, handle=?, title=?, body=?, vendor=?, type=?, tags=?, barcode=?, grams=?, img_src=?, img_src_2=?, img_src_3=?, option1_name=?, option1_value=?, option2_name=?, option2_value=? WHERE id=?");
$stmt_insert = $conn->prepare("INSERT INTO shopifyproducts (generated_sku, variant_sku, handle, title, body, vendor, type, tags, price_huf, qty_location_1, qty_location_2, barcode, grams, img_src, img_src_2, img_src_3, option1_name, option1_value, option2_name, option2_value, needs_update, last_seen_in_feed) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$counter = 0;
$max_test_rows = 5; // CSAK 5 SOR (1-2 termék)

foreach ($feeds as $feed) {
    echo "<hr><h3>Feed: {$feed['url']}</h3>";
    
    $content = @file_get_contents($feed['url']);
    if (!$content) { echo "Hiba a feed letöltésekor!\n"; continue; }
    $temp = fopen('php://memory', 'r+');
    fwrite($temp, $content); rewind($temp);

    $headers = fgetcsv($temp, 0, ",", "\"", "\\");
    $map = array_flip(array_map('strtolower', $headers));

    $required = ['handle', 'vendor', 'variant sku', 'variant price', strtolower($feed['qty_col'])];
    foreach ($required as $r) if (!isset($map[$r])) { echo "Hiányzó oszlop: $r\n"; continue 2; }

    while (($data = fgetcsv($temp, 0, ",", "\"", "\\")) !== FALSE && $counter < $max_test_rows) {
        $counter++;
        $total_rows++;

        $variantSkuGroup = strtolower(trim($data[$map['variant sku']]));
        if (empty($variantSkuGroup)) { echo "ÜRES SKU → átugorva (sor: $total_rows)\n"; continue; }

        $option1Val = trim($data[$map['option1 value'] ?? ''] ?? '');
        $option2Val = trim($data[$map['option2 value'] ?? ''] ?? '');
        
        $generated_sku = $variantSkuGroup;
        if ($option1Val) $generated_sku .= "-" . preg_replace('/[^a-z0-9]+/', '-', strtolower($option1Val));
        if ($option2Val) $generated_sku .= "-" . preg_replace('/[^a-z0-9]+/', '-', strtolower($option2Val));
        
        $csv_price = (float)($data[$map['variant price']] ?? 0);
        $final_price = $csv_price;

        $qty1 = ($feed['location'] == 1) ? (int)$data[$map[strtolower($feed['qty_col'])]] : 0;
        $qty2 = ($feed['location'] == 2) ? (int)$data[$map[strtolower($feed['qty_col'])]] : 0;

        $stmt_check->bind_param("s", $generated_sku);
        $stmt_check->execute();
        $res = $stmt_check->get_result();

        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            if ($feed['location'] == 1) { $qty2 = $row['qty_location_2']; } else { $qty1 = $row['qty_location_1']; }

            $needs_update = ($row['price_huf'] != $final_price || $row['qty_location_1'] != $qty1 || $row['qty_location_2'] != $qty2) ? 10 : $row['needs_update'];

            $stmt_update->bind_param("diiisssssssssssssssssi",
                $final_price, $qty1, $qty2, $needs_update, $run_timestamp,
                $variantSkuGroup, $generated_sku, 
                $data[$map['handle']], $data[$map['title']], $data[$map['body (html)'] ?? ''],
                $data[$map['vendor']], $data[$map['type']], $data[$map['tags']],
                $data[$map['variant barcode'] ?? ''], $data[$map['variant grams'] ?? ''],
                $data[$map['image src'] ?? ''], $data[$map['image src 2'] ?? ''], $data[$map['image src 3'] ?? ''],
                $data[$map['option1 name'] ?? ''], $option1Val, $data[$map['option2 name'] ?? ''], $option2Val,
                $row['id']
            );
            $stmt_update->execute();
            $total_updated++;
        } else {
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
            $stmt_insert->execute();
        }
    }
    fclose($temp);
}

$archive_sql = "UPDATE shopifyproducts SET needs_update = 20 WHERE last_seen_in_feed < ? AND needs_update NOT IN (2, 10)";
$stmt_archive = $conn->prepare($archive_sql);
$stmt_archive->bind_param("s", $run_timestamp);
$stmt_archive->execute();

echo "<hr><b>Feldolgozva: $total_rows | Új: $total_created | Frissítve: $total_updated</b>\n";
echo "<h2>1. LÉPÉS KÉSZ – TESZT MÓD</h2></pre>";
$conn->close();
?>
