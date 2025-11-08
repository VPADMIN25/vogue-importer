<?php
// index2new.php – 2024-04 KÉSZ VERZIÓ

ini_set('max_execution_time', 0);
echo "<h2>2. LÉPÉS – ÚJ TERMÉKEK LÉTREHOZÁSA</h2>";

require_once("helpers/shopifyGraphQL.php");
require_once("helpers/general.php");

// DB
$conn = mysqli_connect(
    getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_PASS'),
    getenv('DB_NAME'), getenv('DB_PORT') ?: 3306
);
mysqli_set_charset($conn, "utf8mb4");

// Shopify
$shopurl = getenv('SHOPIFY_SHOP_URL');
$token   = getenv('SHOPIFY_API_TOKEN');

// Raktárak
$loc1 = getShopifyLocationGid($token,$shopurl,"Italy Vogue Premiere Warehouse 1");
$loc2 = getShopifyLocationGid($token,$shopurl,"Italy Vogue Premiere Warehouse 2");

$sql = "SELECT DISTINCT variant_sku FROM shopifyproducts WHERE needs_update=2 LIMIT 1";
$groups = $conn->query($sql);

while ($g = $groups->fetch_assoc()) {
    $skuGroup = $g['variant_sku'];
    
    // JAVÍTÁS: Ellenőrizzük, hogy a variant_sku nem üres-e
    if (empty($skuGroup)) {
        echo "<hr><b>HIBA: Üres 'variant_sku' sort találtam (needs_update=2), átugrom.</b><br>";
        continue; // Ugrás a következő SKU csoportra
    }

    echo "<hr><b>$skuGroup</b><br>";

    $stmt = $conn->prepare("SELECT * FROM shopifyproducts WHERE variant_sku=? AND needs_update=2");
    $stmt->bind_param("s",$skuGroup); $stmt->execute();
    $res = $stmt->get_result();

    $first = $res->fetch_assoc();
    $handle = sanitize_handle($first['handle'] ?: $first['variant_sku']);
    $images = []; $variants = []; $options = []; $localIds = []; $qtySets = [];

    do {
        $localIds[] = $first['id'];

        // KÉPEK
        foreach (['img_src','img_src_2','img_src_3'] as $k)
            if (!empty($first[$k])) $images[] = ["originalSource"=>$first[$k],"mediaContentType"=>"IMAGE"];

        // VARIÁNSOK
        $opt = [];
        if (!empty($first['option1_value'])) { $opt[] = $first['option1_value']; $options[] = $first['option1_name']; }
        if (!empty($first['option2_value'])) { $opt[] = $first['option2_value']; $options[] = $first['option2_name']; }

        $variants[] = [
            "sku" => $first['generated_sku'],
            "price" => (string)$first['price_huf'],
            "inventoryPolicy" => "DENY",
            "option1" => $opt[0] ?? null,
            "option2" => $opt[1] ?? null,
            "inventoryQuantities" => [
                ["locationId"=>$loc1,"availableQuantity"=>(int)$first['qty_location_1']],
                ["locationId"=>$loc2,"availableQuantity"=>(int)$first['qty_location_2']]
            ]
        ];
    } while ($first = $res->fetch_assoc());
    $stmt->close();

    $images  = array_unique($images, SORT_REGULAR);
    $options = array_values(array_unique($options));

    // 1. TERMÉK VÁZ
    $productInput = [
        "title" => $first['title'],
        "handle" => $handle,
        "descriptionHtml" => $first['body'],
        "vendor" => $first['vendor'],
        "productType" => $first['type'],
        "tags" => explode(',',$first['tags'] ?? ''),
        "status" => "DRAFT"
    ];

    $resp = productCreate_graphql($token,$shopurl,$productInput,$images);
    if (empty($resp['data']['productCreate']['product']['id'])) {
        echo "HIBA: váz<br><pre>".json_encode($resp,JSON_PRETTY_PRINT)."</pre>"; continue;
    }
    $pid = $resp['data']['productCreate']['product']['id'];
    echo "Váz OK: $pid<br>";

    // 2. OPCIÓK
    if ($options) productAddOptions_graphql($token,$shopurl,$pid,$options);

    // 3. VARIÁNTOK
    $variantInputs = array_map(fn($v)=>[
        "sku"=>$v['sku'], "price"=>$v['price'],
        "inventoryPolicy"=>$v['inventoryPolicy'],
        "option1"=>$v['option1'], "option2"=>$v['option2']
    ], $variants);

    $resp = productVariantsBulkCreate_graphql($token,$shopurl,$pid,$variantInputs);
    $created = $resp['data']['productVariantsBulkCreate']['productVariants'] ?? [];

    // 4. KÉSZLET
    foreach ($created as $i=>$cv) {
        $orig = $variants[$i];
        $qtySets[] = ["inventoryItemId"=>$cv['inventoryItem']['id'], "locationId"=>$loc1, "availableQuantity"=>$orig['inventoryQuantities'][0]['availableQuantity']];
        $qtySets[] = ["inventoryItemId"=>$cv['inventoryItem']['id'], "locationId"=>$loc2, "availableQuantity"=>$orig['inventoryQuantities'][1]['availableQuantity']];
    }
    if ($qtySets) inventorySetQuantities_graphql($token,$shopurl,$qtySets);

    // 5. AKTIVÁLÁS
    productActivate_graphql($token,$shopurl,$pid);

    // 6. DB FRISSÍTÉS
    foreach ($created as $i=>$cv) {
        $sku = $cv['sku'];
        $upd = $conn->prepare("UPDATE shopifyproducts SET
            shopifyproductid=?, shopifyvariantid=?, shopifyinventoryid=?, needs_update=0
            WHERE generated_sku=?");
        $upd->bind_param("ssss",$pid,$cv['id'],$cv['inventoryItem']['id'],$sku);
        $upd->execute(); $upd->close();
    }
    echo " kész<br>";
}

echo "<h2>2. LÉPÉS KÉSZ</h2>";
$conn->close();

function sanitize_handle($t){
    return trim(preg_replace('/[^a-z0-9]+/','-',strtolower($t)),'-') ?: 'product';
}
?>


