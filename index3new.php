<?php
// index3new.php (Végleges Verzió V3 - A "Végrehajtó")
// FELADAT: Kezeli a Shopify-ban a módosításokat (Frissít, Javít, Archivál, Reaktivál)

ini_set('max_execution_time', 0);
set_time_limit(0);

echo "<h2>FUTÁS INDUL: 3. Lépés - MÓDOSÍTÁSOK VÉGREHAJTÁSA</h2>";

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

// --- 2. HELPERS ÉS SHOPIFY KREDENCIÁLISOK ---
// A szükséges API függvények miatt kötelező
require_once("helpers/shopifyGraphQL.php");
$shopurl = getenv('SHOPIFY_SHOP_URL');
$token = getenv('SHOPIFY_API_TOKEN');
if (empty($shopurl) || empty($token)) {
    die("❌ Hiányzó Környezeti Változók: SHOPIFY_SHOP_URL vagy SHOPIFY_API_TOKEN.");
}
echo "✅ Shopify kredenciálisok betöltve ($shopurl).<br>";

// --- 3. RAKTÁRHELYEK (LOCATIONS) MEGSZERZÉSE ---
$location_name_1 = "Italy Vogue Premiere Warehouse 1";
$location_name_2 = "Italy Vogue Premiere Warehouse 2";
$location_gid_1 = getShopifyLocationGid($token, $shopurl, $location_name_1);
$location_gid_2 = getShopifyLocationGid($token, $shopurl, $location_name_2);
if (empty($location_gid_1) || empty($location_gid_2)) {
    die("❌ Kritikus hiba: A '$location_name_1' vagy '$location_name_2' raktárhely nem található!");
}
echo "✅ Raktárhely GID-jei sikeresen lekérdezve.<br>";

// --- 4. FELADATOK ÖSSZEGYŰJTÉSE (TÖMEGES VÉGREHAJTÁSHOZ) ---
// Lekérdezünk minden "piszkos" sort (1=Frissítés, 10=Javítás/Felülírás, 20=Archiválás)
$sql = "SELECT * FROM shopifyproducts 
        WHERE needs_update IN (1, 10, 20) 
        AND shopifyproductid IS NOT NULL 
        LIMIT 200"; // Egyszerre csak 200-at
        
$result = $conn->query($sql);

if (!$result) {
    die("❌ Hiba a lekérdezés során: " . $conn->error);
}
if ($result->num_rows == 0) {
    echo "✅ Nincs frissítésre, javításra vagy archiválásra váró termék.<br>";
    $conn->close();
    exit;
}

echo "ℹ️ Feldolgozás alatt: <b>{$result->num_rows}</b> tétel...<br>";

// Várólisták a tömeges (bulk) API hívásokhoz
$inventory_update_queue = [];
$price_update_queue = []; // Kulcs: product_gid, Érték: variáns tömb
$status_archive_queue = [];
$status_reactivate_queue = [];
$full_overwrite_queue = [];

$processed_ids_success = []; 

while ($row = $result->fetch_assoc()) {
    
    switch ($row['needs_update']) {
        
        // Eset 1: Készlet/Ár Frissítés (és REAKTIVÁLÁS)
        case 1:
            // 1a. Ár hozzáadása a várólistához
            $product_gid = $row['shopifyproductid'];
            if (!isset($price_update_queue[$product_gid])) $price_update_queue[$product_gid] = [];
            $price_update_queue[$product_gid][] = [
                "id" => $row['shopifyvariantid'],
                "price" => $row['price_huf']
            ];
            
            // 1b. Készlet hozzáadása a várólistához (mindkét raktár)
            $inventory_gid = $row['shopifyinventoryid'];
            $inventory_update_queue[] = ["inventoryItemId" => $inventory_gid, "locationId" => $location_gid_1, "availableQuantity" => (int)$row['qty_location_1']];
            $inventory_update_queue[] = ["inventoryItemId" => $inventory_gid, "locationId" => $location_gid_2, "availableQuantity" => (int)$row['qty_location_2']];
            
            // 1c. Reaktiválási lista (Ha archivált volt, active-ra állítjuk)
            $status_reactivate_queue[] = $product_gid;
            $processed_ids_success[] = $row['id']; 
            break;
            
        // Eset 10: Teljes Felülírás (Javítás / Örökbefogadás)
        case 10:
            $full_overwrite_queue[] = $row; // Ezt egyenként kell futtatni
            break;
            
        // Eset 20: Archiválás
        case 20:
            $status_archive_queue[] = $row['shopifyproductid'];
            break;
    }
}

// --- 5. TÖMEGES VÉGREHAJTÁSOK ---

// 5A. ÁRAK FRISSÍTÉSE (TÖMEGES)
if (!empty($price_update_queue)) {
    echo "<hr><h4>5A. Árak frissítése...</h4>";
    foreach($price_update_queue as $product_gid => $variants) {
        $response = productVariantsBulkUpdate_graphql($token, $shopurl, $product_gid, $variants);
        if (isset($response['data']['productVariantsBulkUpdate']['userErrors']) && !empty($response['data']['productVariantsBulkUpdate']['userErrors'])) {
             echo "....❌ Hiba: " . json_encode($response['data']['productVariantsBulkUpdate']['userErrors']) . "<br>";
        }
    }
}

// 5B. KÉSZLETEK FRISSÍTÉSE (TÖMEGES)
if (!empty($inventory_update_queue)) {
    echo "<hr><h4>5B. Készletek frissítése...</h4>";
    foreach(array_chunk($inventory_update_queue, 100) as $chunk) {
        $response = inventorySetQuantities_graphql($token, $shopurl, $chunk);
        if (isset($response['data']['inventorySetQuantities']['userErrors']) && !empty($response['data']['inventorySetQuantities']['userErrors'])) {
             echo "....❌ Hiba: " . json_encode($response['data']['inventorySetQuantities']['userErrors']) . "<br>";
        }
    }
}

// 5C. STÁTUSZ: REAKTIVÁLÁS (TÖMEGES)
if (!empty($status_reactivate_queue)) {
    echo "<hr><h4>5C. Termékek reaktiválása...</h4>";
    foreach(array_unique($status_reactivate_queue) as $product_gid) {
        productUpdateStatus_graphql($token, $shopurl, $product_gid, 'ACTIVE');
    }
}

// 5D. STÁTUSZ: ARCHIVÁLÁS (TÖMEGES)
if (!empty($status_archive_queue)) {
    echo "<hr><h4>5D. Termékek archiválása...</h4>";
    foreach(array_unique($status_archive_queue) as $product_gid) {
        $response = productUpdateStatus_graphql($token, $shopurl, $product_gid, 'ARCHIVED');
        
        if (!isset($response['data']['productUpdate']['product']['id'])) {
            // Hiba: ha nem sikerül, a termék valószínűleg már törölt. Állítsuk vissza 0-ra a hibás GID-t, hogy ne próbálja újra.
            $conn->query("UPDATE shopifyproducts SET needs_update = 0 WHERE shopifyproductid = '" . $conn->real_escape_string($product_gid) . "'");
        }
    }
}

// 5E. TELJES FELÜLÍRÁS (EGYENKÉNT)
if (!empty($full_overwrite_queue)) {
    echo "<hr><h4>5E. Teljes felülírás (Javítás/Örökbefogadás)...</h4>";
    foreach($full_overwrite_queue as $row) {
        $product_gid = $row['shopifyproductid'];
        
        // 1. Termék-szintű adatok felülírása (cím, leírás, tagek, képek)
        $product_data = [
            "id" => $product_gid,
            "title" => $row['title'],
            "bodyHtml" => $row['body'],
            "vendor" => $row['vendor'],
            "productType" => $row['type'],
            "tags" => $row['tags'],
            "status" => "ACTIVE" 
        ];
        
        $images_data = [];
        if (!empty($row['img_src'])) $images_data[] = ["src" => $row['img_src']];
        if (!empty($row['img_src_2'])) $images_data[] = ["src" => $row['img_src_2']];
        if (!empty($row['img_src_3'])) $images_data[] = ["src" => $row['img_src_3']];
        if (!empty($images_data)) {
            $product_data["images"] = $images_data;
        }

        $response = productFullUpdate_graphql($token, $shopurl, $product_gid, $product_data);
        
        if (isset($response['data']['productUpdate']['userErrors']) && !empty($response['data']['productUpdate']['userErrors'])) {
             echo "....❌ Hiba (Termék szint): " . json_encode($response['data']['productUpdate']['userErrors']) . "<br>";
             continue; 
        }

        // 2. Variáns-szintű adatok (Ár, Készlet) frissítése
        $variant_data = [
            "id" => $row['shopifyvariantid'],
            "price" => $row['price_huf']
        ];
        productVariantsBulkUpdate_graphql($token, $shopurl, $product_gid, [$variant_data]);
        
        $inventory_data = [
            ["inventoryItemId" => $row['shopifyinventoryid'], "locationId" => $location_gid_1, "availableQuantity" => (int)$row['qty_location_1']],
            ["inventoryItemId" => $row['shopifyinventoryid'], "locationId" => $location_gid_2, "availableQuantity" => (int)$row['qty_location_2']]
        ];
        inventorySetQuantities_graphql($token, $shopurl, $inventory_data);

        $processed_ids_success[] = $row['id'];
    }
}

// --- 6. "ZÁSZLÓK" TISZTÍTÁSA A DB-BEN ---
if (!empty($processed_ids_success)) {
    $ids_string = implode(',', $processed_ids_success);
    $conn->query("UPDATE shopifyproducts SET needs_update = 0 WHERE id IN ($ids_string)");
    echo "<hr>✅ Sikeresen frissítve (needs_update=0): " . count($processed_ids_success) . " tétel.<br>";
}

echo "<h2>✅ Befejezve: 3. Lépés - MÓDOSÍTÁSOK VÉGREHAJTÁSA</h2>";
$conn->close();
?>
