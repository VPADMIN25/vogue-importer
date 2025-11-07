<?php
ini_set('max_execution_time', 0);
set_time_limit(0);

// ✅ Add compatibility for PHP < 8
if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle) {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }
}

// ✅ Database Connection (Using DigitalOcean Environment Variables - VPC Default Mode)
$host = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASS');
$dbname = getenv('DB_NAME');
$port = (int)getenv('DB_PORT');
$sslmode = getenv('DB_SSLMODE'); // 'REQUIRED'

// Létrehozzuk az objektumot
$conn = mysqli_init();

// NEM állítunk be semmilyen kézi SSL opciót
// A rendszerre bízzuk a belső hálózati kapcsolat kezelését

// Csatlakozás a mysqli_real_connect segítségével, SSL flag NÉLKÜL
// Az sslmode='require' miatt a hostnév fogja kikényszeríteni az SSL-t, ha kell.
if (!mysqli_real_connect($conn, $host, $username, $password, $dbname, $port)) {
    // Ha a kapcsolat sikertelen, írjuk ki a hibát és álljunk le
    die("❌ Connection failed (VPC Default Mode Failed): " . mysqli_connect_error());
}

// Ha a kapcsolat sikeres, állítsuk be a karakterkódolást
mysqli_set_charset($conn, "utf8");
echo "✅ Database Connected Successfully<br>";

// ✅ Remote feed URL
$feedUrl = "https://voguepremiere-csv-storage.fra1.digitaloceanspaces.com/peppela_final_feed_huf.csv";

// ✅ Download CSV feed
$tempCsv = sys_get_temp_dir() . "/feed_" . time() . ".csv";
$fileContent = @file_get_contents($feedUrl);

if ($fileContent === false) {
    die("❌ Failed to fetch feed from URL: $feedUrl");
}

// Save feed temporarily
file_put_contents($tempCsv, $fileContent);
echo "✅ Feed downloaded successfully<br>";

// ✅ Open CSV feed
if (($handle = fopen($tempCsv, "r")) !== FALSE) {
    $headers = fgetcsv($handle, 10000, ",", "\"");
  $normalizedHeaders = array_map(function($h) {
    return strtolower(trim($h));
}, $headers);

    // Allowed DB fields
    $dbFields = [
        "title","description","item_specific","condition_val","condition_note",
        "brand","product_type","storecategoryid","storecategoryid2",
        "option1name","option2name","ebayitemid","shopifyproductid",
        "newflag","quantityflag","priceflag","block","duplicate","deleted",
        "status","errdetails","site","channel_id","searchstring","sellerid"
    ];

    // Header mapping
    $customMap = [
        "type"            => "product_type",
        "handle"          => "handle",
        "option1 name"    => "option1name",
        "option2 name"    => "option2name",
        "body (html)"     => "description",
        "tags"            => "tags",
    ];

    $mapping = [];
    foreach ($normalizedHeaders as $index => $headerLower) {
        if (isset($customMap[$headerLower])) {
            $mapping[$index] = $customMap[$headerLower];
        } elseif (in_array($headerLower, $dbFields)) {
            $mapping[$index] = $headerLower;
        }
    }

    $imageColumns = ["image src", "image src 2", "image src 3"];
    $rowCount = 0;
    $skippedCount = 0;

    echo "<br>🟢 Importing new products...<br>";

    // ✅ Read each row from feed
while (($data = fgetcsv($handle, 10000, ",", "\"")) !== FALSE) {

    $insertData = [];
    $descriptionValue = "";
    $tagsValue = "";

    // Adatok beolvasása a "mapping" alapján
    foreach ($mapping as $index => $field) {
        if (!isset($data[$index])) continue;

        $value = $data[$index]; // Nem escape-elünk még, majd csak SQL-nél

        if ($field === "description") {
            $descriptionValue = "<body>" . $value . "</body>";
        }
        if ($field === "tags") {
            $tagsValue = $value;
        }

        $insertData[$field] = $value;
    }

    // ✅ Kulcsmezők ellenőrzése (A TE LOGIKÁD SZERINT)
    if (empty($insertData['variant sku'])) {
        echo "⚠️ Skipping row: Missing Variant SKU<br>";
        continue;
    }

    $handle = isset($insertData['handle']) ? $conn->real_escape_string($insertData['handle']) : "";
    $variantSku = $conn->real_escape_string($insertData['variant sku']);
    $product_id_to_use = 0;

    // ✅ 1. LÉPÉS: Létezik már ez a TERMÉKCSALÁD (Variant SKU)?
    // (Itt kijavítva 'id'-ről 'product_id'-re ÉS a kulcs 'Variant SKU'-ra)
    $checkSql = "SELECT product_id FROM products WHERE sku_group = '" . $variantSku . "' LIMIT 1";
    $result = $conn->query($checkSql);

    if ($result && $result->num_rows > 0) {
        // --- A TERMÉKCSALÁD (SKU) MÁR LÉTEZIK ---
        $row = $result->fetch_assoc();
        $product_id_to_use = $row['product_id'];

    } else {
        // --- ÚJ TERMÉKCSALÁD (SKU) ---
        // Hozzuk létre a fő terméket

        // Vegyük ki a fő termék adatait (ezek minden variánsnál ugyanazok)
        $title = isset($insertData['title']) ? $conn->real_escape_string($insertData['title']) : "";
        $brand = isset($insertData['brand']) ? $conn->real_escape_string($insertData['brand']) : "";
        $productType = isset($insertData['product_type']) ? $conn->real_escape_string($insertData['product_type']) : "";
        $tags = $conn->real_escape_string($tagsValue);

        // FONTOS: A 'sku_group' egy új oszlop, ide mentjük a fő SKU-t a csoportosításhoz.
        // A 'Handle'-t is elmentjük, de nem használjuk kulcsként.
        $sql = "INSERT INTO products (Handle, title, brand, product_type, tags, sku_group, user_id, status, newflag, created_at, updated_at) 
                VALUES ('$handle', '$title', '$brand', '$productType', '$tags', '$variantSku', 1, 'Import in Progress', 1, NOW(), NOW())";

        if ($conn->query($sql) === TRUE) {
            $product_id_to_use = $conn->insert_id; // Megvan az új termék ID-ja
            echo "✅ Inserted NEW product (SKU Group): <b>$variantSku</b> (ProductID: $product_id_to_use)<br>";
            $rowCount++;

            // Leírás hozzáadása (csak egyszer, az új termékhez)
            if (!empty($descriptionValue)) {
                $desc_sql = "INSERT INTO product_description (product_id, description, user_id)
                             VALUES ($product_id_to_use, '" . $conn->real_escape_string($descriptionValue) . "', 1)";
                $conn->query($desc_sql);
            }
        } else {
            echo "❌ Error inserting NEW product ($variantSku): " . $conn->error . "<br>";
            // Ellenőrizzük, hogy a hiba az 'sku_group' oszlop hiánya-e
            if (strpos($conn->error, "Unknown column 'sku_group'") !== false) {
                echo "‼️ KRITIKUS HIBA: A 'products' táblából hiányzik a 'sku_group' oszlop. Kérlek, add hozzá: ALTER TABLE `products` ADD `sku_group` VARCHAR(255) NULL AFTER `Handle`;<br>";
                die(); // Leállítjuk a futást, amíg ezt nem javítod
            }
            continue; // Hiba esetén ugorjunk a következő sorra
        }
    }

    // ✅ 2. LÉPÉS: A VARIÁNS HOZZÁADÁSA (MINDIG)
    if ($product_id_to_use > 0) {

        // Keressük meg a variáns-adatokat
        $option1nameIndex = array_search("option1 name", $normalizedHeaders);
        $option2nameIndex = array_search("option2 name", $normalizedHeaders);
        $option1valIndex = array_search("option1 value", $normalizedHeaders);
        $option2valIndex = array_search("option2 value", $normalizedHeaders);
        $variantPriceIndex = array_search("variant price", $normalizedHeaders);
        // Készlet oszlop dinamikus keresése (mivel a két feedben más a neve)
        $peppelaQtyIndex = array_search("peppela inventory qty", $normalizedHeaders);
        $stockfirmatiQtyIndex = array_search("stockfirmati raktár inventory qty", $normalizedHeaders);

        $variantBarcodeIndex = array_search("variant barcode", $normalizedHeaders); // Ez lesz az egyedi azonosító

        $option1name = $option1nameIndex !== false && isset($data[$option1nameIndex]) ? $conn->real_escape_string($data[$option1nameIndex]) : "";
        $option2name = $option2nameIndex !== false && isset($data[$option2nameIndex]) ? $conn->real_escape_string($data[$option2nameIndex]) : "";
        $option1val = $option1valIndex !== false && isset($data[$option1valIndex]) ? $conn->real_escape_string($data[$option1valIndex]) : "";
        $option2val = $option2valIndex !== false && isset($data[$option2valIndex]) ? $conn->real_escape_string($data[$option2valIndex]) : "";
        $variantPrice = $variantPriceIndex !== false && isset($data[$variantPriceIndex]) ? $conn->real_escape_string($data[$variantPriceIndex]) : 0;
        $variantBarcode = $variantBarcodeIndex !== false && isset($data[$variantBarcodeIndex]) ? $conn->real_escape_string($data[$variantBarcodeIndex]) : $variantSku; // Ha nincs vonalkód, az SKU-t használjuk

        $variantQty = 0;
        if ($peppelaQtyIndex !== false && isset($data[$peppelaQtyIndex])) {
            $variantQty = (int)$data[$peppelaQtyIndex];
        } elseif ($stockfirmatiQtyIndex !== false && isset($data[$stockfirmatiQtyIndex])) {
            $variantQty = (int)$data[$stockfirmatiQtyIndex];
        }

        // Először ellenőrizzük, hogy ez a KONKRÉT VARIÁNS (Vonalkód VAGY opciók) létezik-e már
        $checkVariantSql = "SELECT id FROM product_variants WHERE product_id = $product_id_to_use AND option1val = '$option1val' AND option2val = '$option2val' AND user_id = 1 LIMIT 1";
        $variantResult = $conn->query($checkVariantSql);

        if ($variantResult && $variantResult->num_rows > 0) {
            // Ez a variáns (pl. 'S' méret) már létezik ehhez a termékhez, átugorjuk
            echo "....⏭️ Skipped variant (Option already exists): <b>$option1val / $option2val</b> for SKU Group: $variantSku<br>";
            $skippedCount++;
            continue;
        }

        // --- ÚJ VARIÁNS HOZZÁADÁSA ---

        // Frissítsük a fő termék opcióit (ha még nincsenek beállítva)
        $conn->query("UPDATE products SET option1name = '$option1name', option2name = '$option2name' WHERE product_id = $product_id_to_use AND (option1name IS NULL OR option1name = '')");

        // Illesszük be az új variánst (FIGYELEM: a 'sku' oszlopba a *Variant Barcode*-ot mentjük, mert az az egyedi!)
        $variant_sql = "INSERT INTO product_variants (product_id, option1val, option2val, sku, price, quantity, user_id, updated_at)
                        VALUES ($product_id_to_use, '$option1val', '$option2val', '$variantBarcode', '$variantPrice', '$variantQty', 1, NOW())";

        if ($conn->query($variant_sql) === TRUE) {
            $variant_id = $conn->insert_id;
            echo "....✅ Inserted NEW variant: <b>$option1val / $option2val</b> (Barcode: $variantBarcode) for SKU Group: $variantSku<br>";

            // Képek hozzáadása a variánshoz
            foreach ($imageColumns as $imgCol) {
                $imgIndex = array_search(strtolower($imgCol), $normalizedHeaders);
                if ($imgIndex !== false && isset($data[$imgIndex]) && !empty($data[$imgIndex])) {
                    $imageURL = $conn->real_escape_string($data[$imgIndex]);
                    $img_sql = "INSERT INTO product_images (variant_id, imgurl, user_id)
                                VALUES ($variant_id, '$imageURL', 1)";
                    $conn->query($img_sql);
                }
            }
        } else {
             echo "....❌ Error inserting NEW variant ($option1val / $option2val): " . $conn->error . "<br>";
        }
    } // Vége az if ($product_id_to_use > 0) blokknak
} // Vége a while ciklusnak

    fclose($handle);
    unlink($tempCsv);

    echo "<br>✅ Feed import completed.<br>";
    echo "🟩 New products inserted: <b>$rowCount</b><br>";
    echo "🟨 Skipped duplicates: <b>$skippedCount</b><br>";

} else {
    echo "❌ Failed to open feed file.";
}




$conn->close();
?>







