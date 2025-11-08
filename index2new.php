<?php
// index2new.php – V15 – TELJES ADATOK + HIBAMENTES
ini_set('max_execution_time', 300);
date_default_timezone_set('Europe/Budapest');
echo "<pre style='font-family:Consolas;font-size:14px'><h2>2. LÉPÉS – ÚJ TERMÉKEK (V15 – TELJES ADATOK)</h2>";

// --- KAPCSOLAT ---
$env = [
    'DB_HOST' => $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? 'localhost',
    'DB_USER' => $_ENV['DB_USER'] ?? getenv('DB_USER') ?? 'root',
    'DB_PASS' => $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?? '',
    'DB_NAME' => $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?? 'shopify',
    'DB_PORT' => $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?? 3306,
    'SHOPIFY_SHOP_URL' => $_ENV['SHOPIFY_SHOP_URL'] ?? getenv('SHOPIFY_SHOP_URL') ?? '',
    'SHOPIFY_API_TOKEN' => $_ENV['SHOPIFY_API_TOKEN'] ?? getenv('SHOPIFY_API_TOKEN') ?? '',
];

foreach ($env as $k => $v) if (empty($v)) die("HIÁNYZIK: $k");

require_once("helpers/shopifyGraphQL.php");
require_once("helpers/general.php");

$conn = null;
$maxRetries = 5;
for ($i = 0; $i < $maxRetries; $i++) {
    $conn = @mysqli_connect($env['DB_HOST'], $env['DB_USER'], $env['DB_PASS'], $env['DB_NAME'], (int)$env['DB_PORT']);
    if ($conn) break;
    echo "Kapcsolódás sikertelen... próbálkozás " . ($i + 1) . "/$maxRetries\n";
    sleep(5);
}
if (!$conn) die("FATAL: MySQL hiba!");
mysqli_set_charset($conn, "utf8mb4");
echo "MySQL csatlakozva: {$env['DB_HOST']}:{$env['DB_PORT']}\n";

$shopurl = $env['SHOPIFY_SHOP_URL'];
$token = $env['SHOPIFY_API_TOKEN'];
$loc1 = getShopifyLocationGid($token, $shopurl, "Italy Vogue Premiere Warehouse 1");
$loc2 = getShopifyLocationGid($token, $shopurl, "Italy Vogue Premiere Warehouse 2");
if (!$loc1 || !$loc2) die("HIÁNYZIK: Raktárak");

$LIMIT = 3;
$sql = "SELECT DISTINCT variant_sku FROM shopifyproducts WHERE needs_update=2 AND variant_sku!='' AND title!=''";
if ($LIMIT > 0) $sql .= " LIMIT $LIMIT";

$groups = $conn->query($sql);
if (!$groups || $groups->num_rows == 0) {
    echo "Nincs új termék. <b>Minden OK!</b>\n";
    $conn->close();
    exit;
}

while ($g = $groups->fetch_assoc()) {
    $skuGroup = $g['variant_sku'];
    echo "\n<hr><b style='color:#00f'>$skuGroup</b><br>";

    $stmt = $conn->prepare("SELECT * FROM shopifyproducts WHERE variant_sku=? AND needs_update=2");
    $stmt->bind_param("s", $skuGroup);
    $stmt->execute();
    $res = $stmt->get_result();

    $titleRow = $res->fetch_assoc();
    $title = trim($titleRow['title'] ?? '');
    if (!$title) { echo "Üres cím → átugorva\n"; $stmt->close(); continue; }

    $base = sanitize_handle($titleRow['handle'] ?: $skuGroup);
    $handle = $base;
    $handleFound = false;
    for ($i = 1; $i <= 50; $i++) {
        $test = productCreate_graphql($token, $shopurl, ["title" => "T", "handle" => $handle, "status" => "DRAFT"], []);
        if (!empty($test['data']['productCreate']['product']['id'])) {
            $testId = $test['data']['productCreate']['product']['id'];
            send_graphql_request($token, $shopurl, "mutation { productDelete(input:{id:\"$testId\"}){deletedProductId}}");
            $handleFound = true;
            break;
        }
        $err = $test['data']['productCreate']['userErrors'][0]['message'] ?? '';
        if (!str_contains($err, 'already in use')) break;
        $handle = "$base-$i";
    }
    if (!$handleFound) { echo "Handle nem generálható → átugorva\n"; $stmt->close(); continue; }

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
            "weight" => (float)($r['weight_kg'] ?? 0),
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
        echo "HIBA termék létrehozásánál:<pre>" . json_encode($resp, JSON_PRETTY_PRINT) . "</pre>";
        continue;
    }
    $pid = $resp['data']['productCreate']['product']['id'];
    $num = substr($pid, strrpos($pid, '/') + 1);
    echo "LÉTREHOZVA → <a href='https://$shopurl/admin/products/$num' target='_blank'>$handle</a><br>";

    if ($options) productAddOptions_graphql($token, $shopurl, $pid, $options);

    $varInputs = array_map(fn($v) => array_filter([
        "sku" => $v['sku'],
        "price" => $v['price'],
        "inventoryPolicy" => $v['inventoryPolicy'],
        "requiresShipping" => $v['requiresShipping'],
        "inventoryManagement" => $v['inventoryManagement'],
        "option1" => $v['option1'],
        "option2" => $v['option2'],
        "barcode" => $v['barcode'],
        "weight" => $v['weight'],
        "weightUnit" => $v['weightUnit'],
    ], fn($val) => $val !== null), $variants);

    $resp = productVariantsBulkCreate_graphql($token, $shopurl, $pid, $varInputs);
    $created = $resp['data']['productVariantsBulkCreate']['productVariants'] ?? [];

    $qtySets = [];
    foreach ($created as $i => $cv) {
        $invId = $cv['inventoryItem']['id'];
        $v = $variants[$i];
        if ($v['weight'] > 0) {
            $mutation = "mutation { inventoryItemUpdate(id: \"$invId\", input: { weight: { value: {$v['weight']}, unit: KILOGRAMS } }) { inventoryItem { id } userErrors { field message } } }";
            send_graphql_request($token, $shopurl, $mutation);
        }
        if ($v['qty1'] > 0) $qtySets[] = ["inventoryItemId" => $invId, "locationId" => $loc1, "availableQuantity" => $v['qty1']];
        if ($v['qty2'] > 0) $qtySets[] = ["inventoryItemId" => $invId, "locationId" => $loc2, "availableQuantity" => $v['qty2']];
    }
    if ($qtySets) {
        foreach (array_chunk($qtySets, 100) as $chunk) {
            inventorySetQuantities_graphql($token, $shopurl, $chunk);
        }
    }

    productActivate_graphql($token, $shopurl, $pid);

    foreach ($created as $cv) {
        $upd = $conn->prepare("UPDATE shopifyproducts SET shopifyproductid=?, shopifyvariantid=?, shopifyinventoryid=?, needs_update=0 WHERE generated_sku=?");
        $upd->bind_param("sssss", $pid, $cv['id'], $cv['inventoryItem']['id'], $cv['sku']);
        $upd->execute();
        $upd->close();
    }

    echo "<b style='color:#0f0;background:#000;padding:8px;border-radius:8px'>TELJESEN KÉSZ</b><br>";
}

echo "\n<h2>2. LÉPÉS KÉSZ</h2></pre>";
$conn->close();

function sanitize_handle($t) {
    return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($t ?: 'product')), '-') ?: 'product';
}
?>
