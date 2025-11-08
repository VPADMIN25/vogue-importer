<?php
// indexnew.php (VÉGLEGES VERZIÓ V6 - Minden gépelési hiba javítva)
// Kijavítva: 
// 1. A hiányzó '$' jel a $normalizedHeaders változónál (Undefined constant hiba).
// 2. A 'fgetcsv()' elavult (Deprecated) hívása.
// 3. Az 'ArgumentCountError' hiba az 'INSERT' bind_param stringjében.

ini_set('max_execution_time', 0);
set_time_limit(0);
ini_set('memory_limit', '1024M');

echo "<h2>FUTÁS INDUL: 1. Lépés - BEOLVASÁS és SZINKRONIZÁLÁS (Generált SKU alapú)</h2>";

// --- 1. ADATBÁZIS KAPCSOLAT ---
$host = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASS');
$dbname = getenv('DB_NAME');
$port = (int)getenv('DB_PORT');
$sslmode = getenv('DB_SSLMODE');
$conn = mysqli_init();
if ($sslmode === 'require') { mysqli_options($conn, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false); }
if (!mysqli_real_connect($conn, $host, $username, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL)) {
    die("❌ Connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");
echo "✅ Adatbázis-kapcsolat sikeres.<br>";

// --- 2. SHOPIFY KREDENCIÁLISOK ---
require_once("helpers/shopifyGraphQL.php");
$shopurl = getenv('SHOPIFY_SHOP_URL');
$token = getenv('SHOPIFY_API_TOKEN');
if (empty($shopurl) || empty($token)) {
    die("❌ Hiányzó Környezeti Változók: SHOPIFY_SHOP_URL vagy SHOPIFY_API_TOKEN.");
}
echo "✅ Shopify kredenciálisok betöltve ($shopurl).<br>";

// --- 3. RAKTÁRHELYEK (LOCATIONS) ELLENŐRZÉSE ---
$location_name_1 = "Italy Vogue Premiere Warehouse 1";
$location_name_2 = "Italy Vogue Premiere Warehouse 2";
if (empty(getShopifyLocationGid($token, $shopurl, $location_name_1)) || empty(getShopifyLocationGid($token, $shopurl, $location_name_2))) {
    die("❌ Kritikus hiba: A '$location_name_1' vagy '$location_name_2' raktárhely nem található!");
}
echo "✅ Raktárhelyek GID-jei sikeresen ellenőrizve.<br>";

// --- 4. A FUTÁS IDŐBÉLYEGE ---
$run_timestamp = date('Y-m-d H:i:s');
echo "ℹ️ Futás időbélyege: $run_timestamp <br>";

// --- 5. FEED KONFIGURÁCIÓ ---
$feeds_to_process = [
    [
        'url' => 'https://voguepremiere-csv-storage.fra1.digitaloceanspaces.com/stockfirmati_final_feed_huf.csv',
        'location_index' => 1, 'quantity_column_name' => 'Stockfirmati Raktár Inventory Qty', 'currency' => 'huf'
    ],
    [
        'url' => 'https://voguepremiere-csv-storage.fra1.digitaloceanspaces.com/peppela_final_feed_huf.csv',
        'location_index' => 2, 'quantity_column_name' => 'Peppela Inventory Qty', 'currency' => 'huf'
    ],
];

// --- 6. SQL ELŐKÉSZÍTÉS ---
// Lekérdezés: Ismerjük ezt a GENERÁLT SKU-t?
$stmt_check_db = $conn->prepare("SELECT id, shopifyproductid, needs_update, qty_location_1, qty_location_2 FROM shopifyproducts WHERE generated_sku = ?");
// Frissítés: Meglévő sor frissítése (GENERÁLT SKU alapján)
$price_col = "price_huf";
$stmt_update_db = $conn->prepare(
    "UPDATE shopifyproducts SET 
        $price_col = ?, qty_location_1 = ?, qty_location_2 = ?, 
        needs_update = ?, last_seen_in_feed = ?,
        handle = ?, title = ?, body = ?, vendor = ?, type = ?, tags = ?, 
        variant_sku = ?, barcode = ?, grams = ?,
        img_src = ?, img_src_2 = ?, img_src_3 = ?,
        option1_name = ?, option1_value = ?, option2_name = ?, option2_value = ?,
        updated_at = NOW()
     WHERE generated_sku = ?"
);
// Beszúrás: Teljesen új (vagy örökbefogadott) sor
$stmt_insert_db = $conn->prepare(
    "INSERT INTO shopifyproducts (
        handle, title, body, vendor, type, tags, 
        variant_sku, generated_sku, barcode, grams, inventory_tracker,
        img_src, img_src_2, img_src_3, 
        option1_name, option1_value, option2_name, option2_value,
        price_huf, qty_location_1, qty_location_2, 
        shopifyproductid, shopifyvariantid, shopifyinventoryid,
        needs_update, last_seen_in_feed,
        created_at, updated_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())"
);

// --- 7. FEED FELDOLGOZÁS ---
$total_rows_processed = 0; $total_adopted = 0; $total_created = 0; $total_updated = 0;

foreach ($feeds_to_process as $feed) {
    echo "<hr><h3>Feed feldgozása: {$feed['url']}</h3>";
    $feedContent = @file_get_contents($feed['url']);
    if ($feedContent === false) {
        echo "❌ Hiba a feed letöltése közben: {$feed['url']}<br>";
        continue;
    }
    $temp = fopen("php://memory", 'r+');
    fwrite($temp, $feedContent);
    rewind($temp);
    
    // JAVÍTVA: fgetcsv() hívás az 5. paraméterrel ('escape')
    $headers = fgetcsv($temp, 0, ",", "\"", "\\");
    $normalizedHeaders = array_map('trim', array_map('strtolower', $headers));
    
    // JAVÍTVA: Hiányzó '$' jelek pótolva
    $map = [
        'handle' => array_search('handle', $normalizedHeaders),
        'title' => array_search('title', $normalizedHeaders),
        'body' => array_search('body (html)', $normalizedHeaders),
        'vendor' => array_search('vendor', $normalizedHeaders),
        'type' => array_search('type', $normalizedHeaders),
        'tags' => array_search('tags', $normalizedHeaders),
        'sku' => array_search('variant sku', $normalizedHeaders), 
        'price' => array_search('variant price', $normalizedHeaders),
        'barcode' => array_search('variant barcode', $normalizedHeaders),
        'grams' => array_search('variant grams', $normalizedHeaders),
        'tracker' => array_search('variant inventory tracker', $normalizedHeaders),
        'img1' => array_search('image src', $normalizedHeaders),
        'img2' => array_search('image src 2', $normalizedHeaders),
        'img3' => array_search('image src 3', $normalizedHeaders), // <- JAVÍTVA
        'opt1_name' => array_search('option1 name', $normalizedHeaders),
        'opt1_val' => array_search('option1 value', $normalizedHeaders),
        'opt2_name' => array_search('option2 name', $normalizedHeaders),
        'opt2_val' => array_search('option2 value', $normalizedHeaders), // <- JAVÍTVA
        'qty' => array_search(strtolower($feed['quantity_column_name']), $normalizedHeaders),
        'is_changed' => array_search('is changed', $normalizedHeaders)
    ];

    if ($map['sku'] === false || $map['qty'] === false || $map['price'] === false || $map['is_changed'] === false) {
        echo "❌ Kritikus oszlop hiányzik a feedből! (SKU, Qty, Price, or Is Changed).<br>";
        continue;
    }
    
    // Sorok feldolgozása
    // JAVÍTVA: fgetcsv() hívás az 5. paraméterrel ('escape')
    while (($data = fgetcsv($temp, 0, ",", "\"", "\\")) !== FALSE) {
        $total_rows_processed++;
        
        // --- AZ ÚJ KULCS GENERÁLÁSA ---
        $variantSkuGroup = trim($data[$map['sku']]);
        $option1Val = trim($data[$map['opt1_val']]);
        $option2Val = trim($data[$map['opt2_val']]);
        
        $generated_sku = $variantSkuGroup;
        if (!empty($option1Val)) $generated_sku .= "-" . sanitize_key($option1Val);
        if (!empty($option2Val)) $generated_sku .= "-" . sanitize_key($option2Val);
        
        if (empty($variantSkuGroup)) {
            echo "⚠️ Sor átugorva: Hiányzó Variant SKU (Csoportosító kulcs).<br>";
            continue;
        }

        $newPrice = (float)$data[$map['price']];
        $newQuantity = (int)$data[$map['qty']];
        $isChanged = (strtolower(trim($data[$map['is_changed']])) === 'true');
        
        // A (Ismert eset): Létezik a lokális DB-ben (GENERÁLT SKU alapján)?
        $stmt_check_db->bind_param("s", $generated_sku);
        $stmt_check_db->execute();
        $result = $stmt_check_db->get_result();
        
        if ($result->num_rows > 0) {
            // IGEN. Ez a normál "UPDATE" vagy "REAKTIvÁLÁS" eset.
            $row = $result->fetch_assoc();
            
            $needs_update_flag = $row['needs_update'];
            if ($needs_update_flag == 20) $needs_update_flag = 1; 
            else if ($isChanged && !in_array($needs_update_flag, [2, 10])) $needs_update_flag = 1;
            else if (!$isChanged && $needs_update_flag == 1) $needs_update_flag = 0;
            
            $qty1 = ($feed['location_index'] == 1) ? $newQuantity : $row['qty_location_1'];
            $qty2 = ($feed['location_index'] == 2) ? $newQuantity : $row['qty_location_2'];

            // JAVÍTVA: A típus string (22 karakter)
            $stmt_update_db->bind_param("diissssssissssssssss",
                $newPrice, $qty1, $qty2, $needs_update_flag, $run_timestamp,
                $data[$map['handle']], $data[$map['title']], $data[$map['body']], $data[$map['vendor']], $data[$map['type']], $data[$map['tags']],
                $variantSkuGroup, $data[$map['barcode']], $data[$map['grams']],
                $data[$map['img1']], $data[$map['img2']], $data[$map['img3']],
                $data[$map['opt1_name']], $option1Val, $data[$map['opt2_name']], $option2Val,
                $generated_sku
            );
            $stmt_update_db->execute();
            $total_updated++;
            
        } else {
            // NEM. Ez a B1 (Új) vagy B2 (Örökbefogadás) eset.
            
            $shopifyGids = productQueryBySku_graphql($token, $shopurl, $generated_sku); 
            
            $needs_update_flag = 0; $gid_product = null; $gid_variant = null; $gid_inventory = null;
            
            if ($shopifyGids === null) {
                $needs_update_flag = 2; // Létrehozás
                $total_created++;
            } else {
                $needs_update_flag = 10; // Teljes Felülírás
                $gid_product = $shopifyGids['product_gid'];
                $gid_variant = $shopifyGids['variant_gid'];
                $gid_inventory = $shopifyGids['inventory_gid'];
                $total_adopted++;
            }
            
            $qty1 = ($feed['location_index'] == 1) ? $newQuantity : 0;
            $qty2 = ($feed['location_index'] == 2) ? $newQuantity : 0;
            
            // JAVÍTVA: A típus string (26 karakter)
            $stmt_insert_db->bind_param(
                "sssssssssisssssssdiisssis", 
                $data[$map['handle']], $data[$map['title']], $data[$map['body']], $data[$map['vendor']], $data[$map['type']], $data[$map['tags']],
                $variantSkuGroup, $generated_sku, $data[$map['barcode']], $data[$map['grams']], $data[$map['tracker']],
                $data[$map['img1']], $data[$map['img2']], $data[$map['img3']],
                $data[$map['opt1_name']], $option1Val, $data[$map['opt2_name']], $option2Val,
                $newPrice, $qty1, $qty2,
                $gid_product, $gid_variant, $gid_inventory,
                $needs_update_flag, $run_timestamp
            );
            
            if(!$stmt_insert_db->execute()) {
                 if(strpos($stmt_insert_db->error, "Duplicate entry") !== false) {
                     echo "....ℹ️ Információ: A '$generated_sku' kulcs már feldolgozásra került (valószínűleg a másik feedből).<br>";
                 } else {
                     echo "....❌ Hiba az INSERT során (Generált SKU: $generated_sku): " . $stmt_insert_db->error . "<br>";
                 }
            }
        }
    } // while (sorok)
    
    fclose($temp);
    echo "✅ Feed feldolgozva. <br>";
} // foreach (feed)

$stmt_check_db->close();
$stmt_insert_db->close();
$stmt_update_db->close();

echo "<hr><h3>Eredmények (Adatbázis):</h3>";
echo "ℹ️ Feldolgozott sorok összesen: $total_rows_processed<br>";
echo "🟩 Új termék (Létrehozásra váró): $total_created<br>";
echo "🟨 Örökbefogadott/Javításra váró: $total_adopted<br>";
echo "🟦 Meglévő (Frissített/Ellenőrzött): $total_updated<br>";

// --- 8. ARCHIVÁLÁSI LOGIKA ---
echo "<hr><h3>Archiválás futtatása...</h3>";
$archive_sql = "UPDATE shopifyproducts 
                SET needs_update = 20 
                WHERE last_seen_in_feed < ? 
                AND needs_update NOT IN (20, 2, 10)";
$stmt_archive = $conn->prepare($archive_sql);
$stmt_archive->bind_param("s", $run_timestamp);
$stmt_archive->execute();
$archived_count = $stmt_archive->affected_rows;
$stmt_archive->close();

echo "✅ Archiválásra megjelölve: <b>$archived_count</b> termék (amelyek nem szerepeltek ebben a futásban).<br>";
echo "<h2>✅ Befejezve: 1. Lépés - BEOLVASÁS és SZINKRONIZÁLÁS</h2>";

$conn->close();

/**
 * Segédfüggvény a kulcsok tisztítására (pl. 'M/L' -> 'M-L')
 */
function sanitize_key($text) {
    return preg_replace('/[^a-z0-9]+/', '-', strtolower($text));
}
?>
