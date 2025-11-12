<?php
// index2new.php – TESZT MÓD (1 TERMÉK)
ini_set('max_execution_time', 900);
set_time_limit(900);
ini_set('memory_limit', '2G');

echo "<pre style='font-family:Consolas;font-size:14px'>";
echo "<h2>2. LÉPÉS – ÚJ TERMÉKEK (TESZT: 1 TERMÉK)</h2>";

$env = ['DB_HOST' => getenv('DB_HOST'), 'DB_USER' => getenv('DB_USER'), 'DB_PASS' => getenv('DB_PASS'), 'DB_NAME' => getenv('DB_NAME'), 'DB_PORT' => (int)getenv('DB_PORT')];

$conn = @mysqli_connect($env['DB_HOST'], $env['DB_USER'], $env['DB_PASS'], $env['DB_NAME'], (int)$env['DB_PORT']);
if (!$conn) die("FATAL: MySQL hiba!");
mysqli_set_charset($conn, "utf8mb4");

require_once("helpers/shopifyGraphQL.php");
$shopurl = getenv('SHOPIFY_SHOP_URL');
$token = getenv('SHOPIFY_API_TOKEN');
if (empty($shopurl) || empty($token)) die("Hiányzó SHOPIFY változók.");

$loc1 = getShopifyLocationGid($token, $shopurl, "Italy Vogue Premiere Warehouse 1");
$loc2 = getShopifyLocationGid($token, $shopurl, "Italy Vogue Premiere Warehouse 2");
if (!$loc1 || !$loc2) die("Raktárak hiányoznak!");

// CSAK 1 TERMÉK
$sql = "SELECT DISTINCT variant_sku FROM shopifyproducts WHERE needs_update=2 AND variant_sku!='' AND vendor!='' LIMIT 1";
$groups = $conn->query($sql);

if (!$groups || $groups->num_rows == 0) {
    echo "Nincs új termék. <b>Minden OK!</b>\n";
    $conn->close();
    exit;
}

$skuGroup = $groups->fetch_assoc()['variant_sku'];
echo "\n<hr><b>TESZT TERMÉK: $skuGroup</b><br>";

$stmt = $conn->prepare("SELECT * FROM shopifyproducts WHERE variant_sku=? AND needs_update=2");
$stmt->bind_param("s", $skuGroup);
$stmt->execute();
$res = $stmt->get_result();

$titleRow = $res->fetch_assoc();
$title = trim($titleRow['vendor'] ?? '');
if (!$title) { $stmt->close(); $conn->close(); die("Üres cím!"); }

$base = sanitize_handle($titleRow['handle'] ?: $skuGroup);
$handle = $base;
for ($i = 1; $i <= 10; $i++) {
    $test = productCreate_graphql($token, $shopurl, ["title" => "T", "handle" => $handle, "status" => "DRAFT"], []);
    if (!empty($test['data']['productCreate']['product']['id'])) {
        $testId = $test['data']['productCreate']['product']['id'];
        send_graphql_request($token, $shopurl, "mutation { productDelete(input:{id:\"$testId\"}){deletedProductId}}");
        break;
    }
    $handle = "$base-$i";
}

$res->data_seek(0);
$images = []; $variants = []; $options = [];
while ($r = $res->fetch_assoc()) {
    foreach (['img_src', 'img_src_2', 'img_src_3'] as $k) {
        if (!empty($r[$k])) $images[] = ["originalSource" => $r[$k], "mediaContentType" => "IMAGE"];
    }
    if (!empty($r['option1_value'])) $options[] = $r['option1_name'];
    if (!empty($r['option2_value'])) $options[] = $r['option2_name'];

    $variants[] = [
        "sku" => $r['generated_sku'] ?? '',
        "price" => number_format((float)($r['price_huf'] ?? 0), 2, '.', ''),
        "inventoryPolicy" => "DENY",
        "requiresShipping" => true,
        "inventoryManagement" => "SHOPIFY",
        "option1" => !empty($r['option1_value']) ? $r['option1_value'] : null,
        "option2" => !empty($r['option2_value']) ? $r['option2_value'] : null,
        "barcode" => !empty($r['barcode']) ? $r['barcode'] : null,
        "weight" => (float)($r['grams'] / 1000 ?? 0),
        "weightUnit" => "KILOGRAMS",
        "qty1" => (int)($r['qty_location_1'] ?? 0),
        "qty2" => (int)($r['qty_location_2'] ?? 0),
    ];
}
$stmt->close();

$images = array_values(array_unique($images, SORT_REGULAR));
$options = array_values(array_unique(array_filter($options)));
$tags = array_filter(array_map('trim', explode(',', $titleRow['tags'] ?? '')));

$input = [
    "title" => $title,
    "handle" => $handle,
    "descriptionHtml" => $titleRow['body'] ?? '',
    "vendor" => $titleRow['vendor'] ?? 'Unknown',
    "productType" => $titleRow['type'] ?? 'Clothing',
    "tags" => $tags,
    "status" => "DRAFT"
];

$resp = productCreate_graphql($token, $shopurl, $input, $images);
if (empty($resp['data']['productCreate']['product']['id'])) {
    echo "HIBA: termék létrehozása sikertelen\n";
    $conn->close();
    exit;
}
$pid = $resp['data']['productCreate']['product']['id'];
$num = substr($pid, strrpos($pid, '/') + 1);
echo "LÉTREHOZVA → <a href='https://$shopurl/admin/products/$num'>$handle</a><br>";

if ($options) productAddOptions_graphql($token, $shopurl, $pid, $options);

$varInputs = array_map(fn($v) => array_filter([
    "sku" => $v['sku'], "price" => $v['price'], "inventoryPolicy" => $v['inventoryPolicy'],
    "requiresShipping" => $v['requiresShipping'], "inventoryManagement" => $v['inventoryManagement'],
    "option1" => $v['option1'], "option2" => $v['option2'], "barcode" => $v['barcode'],
    "weight" => $v['weight'], "weightUnit" => $v['weightUnit']
], fn($val) => $val !== null), $variants);

$resp = productVariantsBulkCreate_graphql($token, $shopurl, $pid, $varInputs);
$created = $resp['data']['productVariantsBulkCreate']['productVariants'] ?? [];

$qtySets = [];
foreach ($created as $i => $cv) {
    $invId = $cv['inventoryItem']['id'];
    $v = $variants[$i];
    if ($v['qty1'] > 0) $qtySets[] = ["inventoryItemId" => $invId, "locationId" => $loc1, "availableQuantity" => $v['qty1']];
    if ($v['qty2'] > 0) $qtySets[] = ["inventoryItemId" => $invId, "locationId" => $loc2, "availableQuantity" => $v['qty2']];
}
if ($qtySets) inventorySetQuantities_graphql($token, $shopurl, $qtySets);

productActivate_graphql($token, $shopurl, $pid);

foreach ($created as $cv) {
    $upd = $conn->prepare("UPDATE shopifyproducts SET shopifyproductid=?, shopifyvariantid=?, shopifyinventoryid=?, needs_update=0 WHERE generated_sku=?");
    $upd->bind_param("ssss", $pid, $cv['id'], $cv['inventoryItem']['id'], $cv['sku']);
    $upd->execute();
    $upd->close();
}

echo "<h2>2. LÉPÉS KÉSZ – 1 TERMÉK LÉTREHOZVA</h2></pre>";
$conn->close();

function sanitize_handle($t) {
    return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($t ?: 'product')), '-') ?: 'product';
}
?>
