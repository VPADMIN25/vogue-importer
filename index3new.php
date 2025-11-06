<?php
ini_set('max_execution_time', 0);
set_time_limit(0);

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
    // A DigitalOcean futtatókörnyezete automatikusan kezeli a CA certifikátumot
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

// ✅ Feed URL
$feedUrl = "https://voguepremiere-csv-storage.fra1.digitaloceanspaces.com/peppela_final_feed_huf.csv";
echo "📥 Fetching feed from: $feedUrl<br>";

// ✅ Read feed content
$feedContent = @file_get_contents($feedUrl);
if ($feedContent === false) {
    die("❌ Unable to fetch feed: $feedUrl");
}

// ✅ Parse CSV from feed
$rows = [];
$temp = fopen("php://memory", 'r+');
fwrite($temp, $feedContent);
rewind($temp);

while (($data = fgetcsv($temp, 20000, ",")) !== FALSE) {
    $rows[] = $data;
}
fclose($temp);

if (count($rows) <= 1) {
    die("❌ No data found in feed.");
}

// ✅ Shopify credentials
$shopurl = '';
$token = '';
$userresult = $conn->query("SELECT * FROM users WHERE installationstatus = 1 AND id = 1");
if ($userresult->num_rows > 0) {
    while ($userrow = $userresult->fetch_assoc()) {
        $shopurl = $userrow['shopurl'];
        $token = $userrow['token'];
    }
}

// ✅ Counters
$insertedProducts = 0;
$insertedVariants = 0;
$insertedDescriptions = 0;
$insertedImages = 0;
$updatedProducts = 0;
$skipped = 0;

// ✅ Process rows (skip header)
for ($row = 1; $row < count($rows); $row++) {
    $data = $rows[$row];

    $handleVal      = $conn->real_escape_string(trim($data[0]));
    $title          = $conn->real_escape_string(trim($data[1]));
    $description    = $conn->real_escape_string(trim($data[2]));
    $brand          = $conn->real_escape_string(trim($data[3]));
    $productType    = $conn->real_escape_string(trim($data[4]));
    $option1Name    = $conn->real_escape_string(trim($data[7]));
    $option1Value   = $conn->real_escape_string(trim($data[8]));
    $option2Name    = $conn->real_escape_string(trim($data[9]));
    $option2Value   = $conn->real_escape_string(trim($data[10]));
    $variantSku     = $conn->real_escape_string(trim($data[11]));
    $inventoryQty   = intval($data[14]);
    $variantPrice   = floatval($data[16]);
    $isChanged      = trim($data[26]);

    $imageurl1      = $conn->real_escape_string(trim($data[20]));
    $imageurl2      = isset($data[21]) ? $conn->real_escape_string(trim($data[21])) : '';
    $imageurl3      = isset($data[22]) ? $conn->real_escape_string(trim($data[22])) : '';

    $user_id = 1;

    if (empty($handleVal)) {
        echo "⚠️ Skipping row $row: Missing handle<br>";
        continue;
    }

    echo "Row $row → Handle: $handleVal | Is Changed: <b>$isChanged</b><br>";

    // ✅ Skip if not changed
    if (strtolower(trim($isChanged)) !== "true") {
        echo "⏭️ Skipped '$handleVal' (Is Changed = FALSE — no update)<br>";
        $skipped++;
        continue;
    }

    // ✅ Check if product exists
    $checkProduct = $conn->query("SELECT product_id FROM products WHERE title = '$title'");
    if ($checkProduct && $checkProduct->num_rows > 0) {
        // ✅ Update existing
        $productRow = $checkProduct->fetch_assoc();
        $product_id = $productRow['product_id'];

        $variantCheck = $conn->query("SELECT * FROM product_variants WHERE product_id = $product_id AND option1val='$option1Value' AND option2val='$option2Value'");
        if ($variantCheck && $variantCheck->num_rows > 0) {
            $variantRow = $variantCheck->fetch_assoc();
            $dbQty = (int)$variantRow['quantity'];
            $dbPrice = (float)$variantRow['price'];
            $shopifyproductid = $variantRow['shopifyproductid'];
            $shopifyvariantid = $variantRow['shopifyvariantid'];
            $shopifyinventoryid = $variantRow['shopifyinventoryid'];
            $shopifylocationid = $variantRow['shopifylocationid'];
        } else {
            $dbQty = 0;
            $dbPrice = 0;
            $shopifyproductid = '';
            $shopifyvariantid = '';
            $shopifyinventoryid = '';
            $shopifylocationid = '';
        }

        echo "🔄 Updating '$handleVal' (Is Changed = TRUE)...<br>";

        // ✅ Update Shopify
        if ($dbQty != $inventoryQty) {
            updateShopifyInventory($token, $shopurl, $shopifyinventoryid, $shopifylocationid, $inventoryQty);
        }
        if (abs($dbPrice - $variantPrice) > 0.001) {
            updateShopifyPrice1($shopurl, $token, $shopifyvariantid, $variantPrice, $shopifyproductid);
        }

        // ✅ Update local DB
        $updateSql = "
            UPDATE product_variants
            SET quantity = $inventoryQty, price = $variantPrice, updated_at = NOW()
            WHERE product_id = $product_id
        ";
        if ($conn->query($updateSql)) {
            echo "✅ Updated '$handleVal' → Qty: $inventoryQty | Price: $variantPrice<br>";
            $updatedProducts++;
        } else {
            echo "❌ DB Update failed for '$handleVal': " . $conn->error . "<br>";
        }

    } else {
        // ✅ Insert new
        echo "🆕 Inserting new product '$handleVal' (Is Changed = TRUE)...<br>";

        $insertProduct = "
            INSERT INTO products (
                title, description, Handle, brand, product_type, option1name, option2name, status, user_id
            ) VALUES (
                '$title', '$description', '$handleVal', '$brand', '$productType', '$option1Name', '$option2Name', 'Import in Progress', $user_id
            )
        ";
        if ($conn->query($insertProduct)) {
            $product_id = $conn->insert_id;
            $insertedProducts++;

            $insertVariant = "
                INSERT INTO product_variants (product_id, option1val, option2val, price, quantity, user_id, updated_at)
                VALUES ($product_id, '$option1Value', '$option2Value', '$variantPrice', '$inventoryQty', 1, NOW())
            ";
            if ($conn->query($insertVariant)) {
                $variant_id = $conn->insert_id;
                $insertedVariants++;

                $conn->query("INSERT INTO product_description (product_id, description, user_id) VALUES ($product_id, '$description', 1)");
                $insertedDescriptions++;

                $imageUrls = array_filter([$imageurl1, $imageurl2, $imageurl3]);
                foreach ($imageUrls as $imgUrl) {
                    $conn->query("INSERT INTO product_images (variant_id, imgurl, user_id) VALUES ($variant_id, '$imgUrl', 1)");
                    $insertedImages++;
                }

                echo "✅ Inserted '$handleVal' → Qty: $inventoryQty | Price: $variantPrice<br>";
            }
        }
    }
}

// ✅ Summary
echo "<br>🎯 Feed Import Completed<br>";
echo "✅ Products Inserted: $insertedProducts<br>";
echo "✅ Variants Inserted: $insertedVariants<br>";
echo "✅ Descriptions Inserted: $insertedDescriptions<br>";
echo "✅ Images Inserted: $insertedImages<br>";
echo "🔄 Products Updated (Is Changed = TRUE): $updatedProducts<br>";
echo "⏩ Skipped (Is Changed = FALSE): $skipped<br>";

$conn->close();


// ✅ Shopify helper functions
function updateShopifyInventory($token, $shopurl, $inventory_item_id, $location_id, $quantity) {
    if (empty($inventory_item_id) || empty($location_id)) return;
    $shopifyinverid = 'gid://shopify/InventoryItem/'.$inventory_item_id;
    $shopifylocatid = 'gid://shopify/Location/'.$location_id;
    $quantity = (int)$quantity;

    $query = <<<'GRAPHQL'
mutation InventorySet($input: InventorySetQuantitiesInput!) {
    inventorySetQuantities(input: $input) {
        userErrors { field message }
    }
}
GRAPHQL;

    $variables = [
        "input" => [
            "ignoreCompareQuantity" => true,
            "name" => "available",
            "reason" => "correction",
            "quantities" => [[
                "inventoryItemId" => $shopifyinverid,
                "locationId" => $shopifylocatid,
                "quantity" => $quantity
            ]]
        ]
    ];

    $payload = json_encode(['query' => $query, 'variables' => $variables]);
    $ch = curl_init("https://$shopurl/admin/api/2024-10/graphql.json");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ["Content-Type: application/json", "X-Shopify-Access-Token: $token"],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload
    ]);
    curl_exec($ch);
    curl_close($ch);
}

function updateShopifyPrice1($shopurl, $token, $shopifyvariantid, $price, $shopifyproductid) {
    if (empty($shopifyvariantid) || empty($shopifyproductid)) return;

    $query = <<<'GRAPHQL'
mutation productVariantsBulkUpdate($productId: ID!, $variants: [ProductVariantsBulkInput!]!) {
    productVariantsBulkUpdate(productId: $productId, variants: $variants) {
        userErrors { field message }
    }
}
GRAPHQL;

    $productId = "gid://shopify/Product/".$shopifyproductid;
    $variants = [["id" => "gid://shopify/ProductVariant/".$shopifyvariantid, "price" => $price]];
    $payload = json_encode(["query" => $query, "variables" => ["productId" => $productId, "variants" => $variants]]);

    $ch = curl_init("https://$shopurl/admin/api/2024-10/graphql.json");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ["Content-Type: application/json", "X-Shopify-Access-Token: $token"],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload
    ]);
    curl_exec($ch);
    curl_close($ch);
}
?>
