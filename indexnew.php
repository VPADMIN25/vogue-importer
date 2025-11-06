<?php
ini_set('max_execution_time', 0);
set_time_limit(0);

// ✅ Add compatibility for PHP < 8
if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle) {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }
}

// ✅ Database Connection (Using DigitalOcean Environment Variables)
$host = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASS');
$dbname = getenv('DB_NAME');
$port = getenv('DB_PORT'); // A DigitalOcean-nél a port elengedhetetlen!
$sslmode = getenv('DB_SSLMODE'); // 'REQUIRED'

// A DigitalOcean Managed MySQL-hez SSL kapcsolat szükséges
$conn = mysqli_init();
if ($sslmode === 'require') {
    // A belső (VPC) kapcsolathoz nincs szükség a ca-cert.crt fájlra,
    // de az SSL flag-re igen.
    mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);
}

// Csatlakozás a mysqli_real_connect segítségével, ami kezeli a portot és az SSL-t
if (!mysqli_real_connect($conn, $host, $username, $password, $dbname, (int)$port, NULL, $sslmode === 'require' ? MYSQLI_CLIENT_SSL : 0)) {
    // Ha a kapcsolat sikertelen, írjuk ki a hibát és álljunk le
    die("❌ Connection failed: " . mysqli_connect_error());
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
    $headers = fgetcsv($handle, 10000, ",");
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
    while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {

        $insertData = [];
        $descriptionValue = "";

        foreach ($mapping as $index => $field) {
            if (!isset($data[$index])) continue;
            $value = $conn->real_escape_string($data[$index]);

            if ($field === "description") {
                $descriptionValue = "<body>" . $value . "</body>";
            }

            $insertData[$field] = $value;
        }

        // ✅ Skip if title is missing
        if (empty($insertData['title'])) {
            echo "⚠️ Skipping product with missing title<br>";
            continue;
        }

        $title = $insertData['title'];

        // ✅ Check if product already exists by title
        $checkSql = "SELECT id FROM products WHERE title = '" . $conn->real_escape_string($title) . "' LIMIT 1";
        $result = $conn->query($checkSql);

        if ($result && $result->num_rows > 0) {
            $skippedCount++;
            continue; // Skip duplicate
        }

        // ✅ Insert new product
        $insertData['user_id'] = 1;
        $insertData['status'] = "Import in Progress";

        $columns = implode(",", array_keys($insertData));
        $values  = "'" . implode("','", array_values($insertData)) . "'";
        $sql = "INSERT INTO products ($columns) VALUES ($values)";

        if ($conn->query($sql) === TRUE) {
            $product_id = $conn->insert_id;

            echo "✅ Inserted product: <b>$title</b><br>";

            // ✅ Insert into product_description
            if (!empty($descriptionValue)) {
                $desc_sql = "INSERT INTO product_description (product_id, description, user_id)
                             VALUES ($product_id, '" . $conn->real_escape_string($descriptionValue) . "', 1)";
                $conn->query($desc_sql);
            }

            // ✅ Variant info
            $option1valIndex = array_search("option1 value", $normalizedHeaders);
            $option2valIndex = array_search("option2 value", $normalizedHeaders);
            $variantPriceIndex = array_search("variant price", $normalizedHeaders);
            $variantQtyIndex = array_search("peppela inventory qty", $normalizedHeaders);
            $variantSkuIndex = array_search("variant sku", $normalizedHeaders);

            $option1val = $option1valIndex !== false && isset($data[$option1valIndex]) ? $conn->real_escape_string($data[$option1valIndex]) : "";
            $option2val = $option2valIndex !== false && isset($data[$option2valIndex]) ? $conn->real_escape_string($data[$option2valIndex]) : "";
            $variantPrice = $variantPriceIndex !== false && isset($data[$variantPriceIndex]) ? $conn->real_escape_string($data[$variantPriceIndex]) : 0;
            $variantQty = $variantQtyIndex !== false && isset($data[$variantQtyIndex]) ? $conn->real_escape_string($data[$variantQtyIndex]) : 0;
            $variantSku = $variantSkuIndex !== false && isset($data[$variantSkuIndex]) ? $conn->real_escape_string($data[$variantSkuIndex]) : "";

            if (!empty($option1val) || !empty($option2val) || !empty($variantSku)) {
                $variant_sql = "INSERT INTO product_variants (product_id, option1val, option2val, sku, price, quantity, user_id)
                                VALUES ($product_id, '$option1val', '$option2val', '$variantSku', '$variantPrice', '$variantQty', 1)";
                if ($conn->query($variant_sql) === TRUE) {
                    $variant_id = $conn->insert_id;

                    foreach ($imageColumns as $imgCol) {
                        $imgIndex = array_search(strtolower($imgCol), $normalizedHeaders);
                        if ($imgIndex !== false && isset($data[$imgIndex]) && !empty($data[$imgIndex])) {
                            $imageURL = $conn->real_escape_string($data[$imgIndex]);
                            $img_sql = "INSERT INTO product_images (variant_id, imgurl, user_id)
                                        VALUES ($variant_id, '$imageURL', 1)";
                            $conn->query($img_sql);
                        }
                    }
                }
            }

            $rowCount++;
        } else {
            echo "❌ Error inserting product ($title): " . $conn->error . "<br>";
        }
    }

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


