<?php
// index2new.php – V10 – VÉGTELEN HANDLE + 0 HIBA

ini_set('max_execution_time', 0);
echo "<h2>2. LÉPÉS – ÚJ TERMÉKEK LÉTREHOZÁSA</h2>";

$LIMIT = 10;  // ← állítsd 1/10/50-re

require_once("helpers/shopifyGraphQL.php");
require_once("helpers/general.php");

$conn = mysqli_connect(getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_PASS'), getenv('DB_NAME'), getenv('DB_PORT') ?: 3306);
mysqli_set_charset($conn, "utf8mb4");

$shopurl = getenv('SHOPIFY_SHOP_URL');
$token   = getenv('SHOPIFY_API_TOKEN');

$loc1 = getShopifyLocationGid($token,$shopurl,"Italy Vogue Premiere Warehouse 1");
$loc2 = getShopifyLocationGid($token,$shopurl,"Italy Vogue Premiere Warehouse 2");

$sql = "SELECT DISTINCT variant_sku FROM shopifyproducts WHERE needs_update=2 AND variant_sku IS NOT NULL AND variant_sku != ''";
if ($LIMIT > 0) $sql .= " LIMIT $LIMIT";

$groups = $conn->query($sql);
if (!$groups || $groups->num_rows == 0) {
    echo "Nincs új termék.<br>";
    exit;
}

while ($g = $groups->fetch_assoc()) {
    $skuGroup = $g['variant_sku'];
    if (empty($skuGroup)) continue;

    echo "<hr><b>$skuGroup</b><br>";

    $stmt = $conn->prepare("SELECT * FROM shopifyproducts WHERE variant_sku=? AND needs_update=2");
    $stmt->bind_param("s", $skuGroup); $stmt->execute(); $res = $stmt->get_result();

    $first = $res->fetch_assoc();
    if (!$first || empty($trim($first['title']))) {
        echo "Nincs cím → átugorva<br>";
        $stmt->close(); continue;
    }

    // ÚJ: VÉGTELEN HANDLE GENERÁTOR
    $base = sanitize_handle($first['handle'] ?: $first['variant_sku']);
    $handle = $base;
    $attempt = 0;

    while (true) {
        $testInput = [
            "title" => "HANDLE_TEST_" . time(),
            "handle" => $handle,
            "status" => "DRAFT"
        ];
        $test = productCreate_graphql($token, $shopurl, $testInput, []);

        // Ha nincs hiba → handle szabad
        if (!empty($test['data']['productCreate']['product']['id'])) {
            // Töröljük a teszt terméket
            $testId = $test['data']['productCreate']['product']['id'];
            $delQ = "mutation { productDelete(input: {id: \"$testId\"}) { deletedProductId } }";
            send_graphql_request($token, $shopurl, $delQ);
            break;
        }

        // Ha handle hiba → újat próbálunk
        $err = $test['data']['productCreate']['userErrors'][0]['message'] ?? '';
        if (str_contains($err, 'already in use')) {
            $attempt++;
            $handle = $base . "-" . $attempt;
            echo "Handle foglalt → új: <b>$handle</b><br>";
            usleep(200000); // 0.2 mp várakozás
            continue;
        }

        // Más hiba → kilépünk
        echo "Váratlan hiba a handle tesztben!<br>";
        break;
    }

    // MOST MÁR BIZTOS SZABAD A HANDLE
    $images = []; $variants = []; $options = []; $qtySets = [];

    do {
        foreach (['img_src','img_src_2','img_src_3'] as $k)
            if (!empty($first[$k])) $images[] = ["originalSource"=>$first[$k],"mediaContentType"=>"IMAGE"];

        if (!empty($first['option1_value'])) $options[] = $first['option1_name'];
        if (!empty($first['option2_value'])) $options[] = $first['option2_name'];

        $variants[] = [
            "sku" => $first['generated_sku'],
            "price" => (string)$first['price_huf'],
            "inventoryPolicy" => "DENY",
            "option1" => $first['option1_value'] ?: null,
            "option2" => $first['option2_value'] ?: null,
            "qty1" => (int)$first['qty_location_1'],
            "qty2" => (int)$first['qty_location_2']
        ];
    } while ($first = $res->fetch_assoc());
    $stmt->close();

    $images = array_values(array_unique($images, SORT_REGULAR));
    $options = array_values(array_unique(array_filter($options)));

    // VALÓDI TERMÉK
    $productInput = [
        "title" => $first['title'],
        "handle" => $handle,
        "descriptionHtml" => $first['body'] ?? '',
        "vendor" => $first['vendor'] ?? 'Unknown',
        "productType" => $first['type'] ?? 'Clothing',
        "tags" => array_filter(explode(',', $first['tags'] ?? '')),
        "status" => "DRAFT"
    ];

    $resp = productCreate_graphql($token,$shopurl,$productInput,$images);
    if (empty($resp['data']['productCreate']['product']['id'])) {
        echo "HIBA: váz<br><pre>".json_encode($resp,JSON_PRETTY_PRINT)."</pre>";
        continue;
    }

    $pid = $resp['data']['productCreate']['product']['id'];
    $productNum = substr($pid, strrpos($pid, '/')+1);
    echo "TERMÉK LÉTREHOZVA → <a href='https://$shopurl/admin/products/$productNum' target='_blank'>$handle</a><br>";

    if ($options) productAddOptions_graphql($token,$shopurl,$pid,$options);

    $variantInputs = array_map(fn($v)=>[
        "sku"=>$v['sku'], "price"=>$v['price'],
        "inventoryPolicy"=>$v['inventoryPolicy'],
        "option1"=>$v['option1'], "option2"=>$v['option2']
    ], $variants);

    $resp = productVariantsBulkCreate_graphql($token,$shopurl,$pid,$variantInputs);
    $created = $resp['data']['productVariantsBulkCreate']['productVariants'] ?? [];

    foreach ($created as $i => $cv) {
        $orig = $variants[$i];
        $qtySets[] = ["inventoryItemId"=>$cv['inventoryItem']['id'], "locationId"=>$loc1, "availableQuantity"=>$orig['qty1']];
        $qtySets[] = ["inventoryItemId"=>$cv['inventoryItem']['id'], "locationId"=>$loc2, "availableQuantity"=>$orig['qty2']];
    }
    if ($qtySets) inventorySetQuantities_graphql($token,$shopurl,$qtySets);

    productActivate_graphql($token,$shopurl,$pid);

    foreach ($created as $cv) {
        $upd = $conn->prepare("UPDATE shopifyproducts SET shopifyproductid=?, shopifyvariantid=?, shopifyinventoryid=?, needs_update=0 WHERE generated_sku=?");
        $upd->bind_param("sssss", $pid, $cv['id'], $cv['inventoryItem']['id'], $cv['sku']);
        $upd->execute(); $upd->close();
    }

    echo "<b style='color:lime;font-size:18px'>TELJESEN KÉSZ – ACTIVE</b><br>";
}

echo "<h2>2. LÉPÉS KÉSZ</h2>";
$conn->close();

function sanitize_handle($t){
    return trim(preg_replace('/[^a-z0-9]+/','-',strtolower($t ?: 'product')),'-') ?: 'product';
}
?>
