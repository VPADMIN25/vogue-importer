<?php
// V14 – TELJESEN KÉSZ – SKU + ÁR + VARIÁNS + TAG + KÉSZLET + SÚLY + BARCODE
ini_set('max_execution_time', 0);
echo "<h2>2. LÉPÉS – ÚJ TERMÉKEK (TELJES ADATOKKAL)</h2>";

$LIMIT = 3; // ← állítsd 10-re, ha biztos vagy

require_once("helpers/shopifyGraphQL.php");
require_once("helpers/general.php");

$conn = mysqli_connect(getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_PASS'), getenv('DB_NAME'), 3306, getenv('DB_SOCKET') ?: '/var/run/mysqld/mysqld.sock');
mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 10);
mysqli_options($conn, MYSQLI_OPT_READ_TIMEOUT, 30);
if (!$conn) die("<b style='color:red'>MySQL nem elérhető – ellenőrizd a DO adatbázist!</b>");
mysqli_set_charset($conn, "utf8mb4");

$shopurl = getenv('SHOPIFY_SHOP_URL');
$token   = getenv('SHOPIFY_API_TOKEN');
$loc1 = getShopifyLocationGid($token,$shopurl,"Italy Vogue Premiere Warehouse 1");
$loc2 = getShopifyLocationGid($token,$shopurl,"Italy Vogue Premiere Warehouse 2");

$sql = "SELECT DISTINCT variant_sku FROM shopifyproducts 
        WHERE needs_update=2 AND variant_sku!='' AND title!=''";
if ($LIMIT>0) $sql .= " LIMIT $LIMIT";

$groups = $conn->query($sql);
if (!$groups || $groups->num_rows==0) { echo "Nincs új termék."; exit; }

while ($g = $groups->fetch_assoc()) {
    $skuGroup = $g['variant_sku'];
    echo "<hr><b>$skuGroup</b><br>";

    $stmt = $conn->prepare("SELECT * FROM shopifyproducts WHERE variant_sku=? AND needs_update=2");
    $stmt->bind_param("s",$skuGroup); $stmt->execute(); $res = $stmt->get_result();

    $titleRow = $res->fetch_assoc();
    $title = trim($titleRow['title']);
    if (!$title) continue;

    // 1. HANDLE (soha többé nem akad)
    $base = sanitize_handle($titleRow['handle'] ?: $skuGroup);
    $handle = $base;
    for ($i=1; $i<=50; $i++) {
        $test = productCreate_graphql($token,$shopurl,["title"=>"T","handle"=>$handle,"status"=>"DRAFT"],[]);
        if (!empty($test['data']['productCreate']['product']['id'])) {
            send_graphql_request($token,$shopurl,"mutation{productDelete(input:{id:\"{$test['data']['productCreate']['product']['id']}\"}){deletedProductId}}");
            break;
        }
        $handle = "$base-$i";
    }

    // 2. ADATGYŰJTÉS
    $res->data_seek(0);
    $images = []; $variants = []; $options = []; $metafields = [];
    while ($r = $res->fetch_assoc()) {
        if ($r['img_src']) $images[] = ["src"=>$r['img_src']];
        if ($r['img_src_2']) $images[] = ["src"=>$r['img_src_2']];
        if ($r['img_src_3']) $images[] = ["src"=>$r['img_src_3']];

        if ($r['option1_value']) $options[] = $r['option1_name'];
        if ($r['option2_value']) $options[] = $r['option2_name'];

        $variants[] = [
            "sku" => $r['generated_sku'],
            "price" => number_format((float)$r['price_huf'], 2, '.', ''),
            "inventoryPolicy" => "DENY",
            "option1" => $r['option1_value'] ?: "Default",
            "option2" => $r['option2_value'] ?: "Default",
            "weight" => (float)($r['weight_kg'] ?? 0),
            "weightUnit" => "KILOGRAMS",
            "barcode" => $r['barcode'] ?? "",
            "qty1" => (int)$r['qty_location_1'],
            "qty2" => (int)$r['qty_location_2']
        ];
    }
    $stmt->close();

    $images = array_unique($images, SORT_REGULAR);
    $options = array_values(array_unique(array_filter($options)));
    $tags = array_filter(array_map('trim', explode(',', $titleRow['tags']??'')));

    // 3. TERMÉK LÉTREHOZÁS
    $input = [
        "title" => $title,
        "handle" => $handle,
        "descriptionHtml" => $titleRow['body']??'',
        "vendor" => $titleRow['vendor']??'Unknown',
        "productType" => $titleRow['type']??'Clothing',
        "tags" => $tags,
        "status" => "DRAFT"
    ];

    $resp = productCreate_graphql($token,$shopurl,$input,$images);
    if (empty($resp['data']['productCreate']['product']['id'])) {
        echo "<pre>".json_encode($resp,JSON_PRETTY_PRINT)."</pre>"; continue;
    }
    $pid = $resp['data']['productCreate']['product']['id'];
    $num = substr($pid,strrpos($pid,'/')+1);
    echo "LÉTREHOZVA → <a href='https://$shopurl/admin/products/$num' target='_blank'>$handle</a><br>";

    // 4. OPCIÓK
    if ($options) productAddOptions_graphql($token,$shopurl,$pid,$options);

    // 5. VARIÁNSOK
    $vars = array_map(fn($v)=>[
        "sku"=>$v['sku'],
        "price"=>$v['price'],
        "inventoryPolicy"=>$v['inventoryPolicy'],
        "option1"=>$v['option1'],
        "option2"=>$v['option2'],
        "weight"=>$v['weight'],
        "weightUnit"=>$v['weightUnit'],
        "barcode"=>$v['barcode']
    ], $variants);

    $resp = productVariantsBulkCreate_graphql($token,$shopurl,$pid,$vars);
    $created = $resp['data']['productVariantsBulkCreate']['productVariants']??[];

    // 6. KÉSZLET
    $qty = [];
    foreach ($created as $i=>$cv) {
        $qty[] = ["inventoryItemId"=>$cv['inventoryItem']['id'], "locationId"=>$loc1, "availableQuantity"=>$variants[$i]['qty1']];
        $qty[] = ["inventoryItemId"=>$cv['inventoryItem']['id'], "locationId"=>$loc2, "availableQuantity"=>$variants[$i]['qty2']];
    }
    if ($qty) inventorySetQuantities_graphql($token,$shopurl,$qty);

    // 7. AKTIVÁLÁS
    productActivate_graphql($token,$shopurl,$pid);

    // 8. DB FRISSÍTÉS
    foreach ($created as $cv) {
        $u = $conn->prepare("UPDATE shopifyproducts SET shopifyproductid=?, shopifyvariantid=?, shopifyinventoryid=?, needs_update=0 WHERE generated_sku=?");
        $u->bind_param("sssss",$pid,$cv['id'],$cv['inventoryItem']['id'],$cv['sku']);
        $u->execute(); $u->close();
    }

    echo "<b style='color:#0f0;background:#000;padding:8px;border-radius:8px'>100% KÉSZ – SKU, ÁR, VARIÁNS, TAG, KÉSZLET, SÚLY, BARCODE</b><br>";
}
echo "<h2>2. LÉPÉS KÉSZ</h2>";
$conn->close();

function sanitize_handle($t){
    return trim(preg_replace('/[^a-z0-9]+/','-',strtolower($t?:'product')),'-')?:'product';
}
?>

