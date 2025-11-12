<?php
// helpers/shopifyGraphQL.php (JAVÍTOTT VERZIÓ: 2024-04 API-val)

ini_set('max_execution_time', 0);
set_time_limit(0);

function send_graphql_request($token, $shopurl, $query, $variables = []) {
    $data = ['query' => $query];
    if (!empty($variables)) $data['variables'] = $variables;

    // 1. JAVÍTÁS: A helyes API verzió biztosítása
    $url = "https://$shopurl/admin/api/2025-10/graphql.json";
    
    $ch = curl_init($url); 
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "X-Shopify-Access-Token: $token"
        ],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data)
    ]);

    $resp = curl_exec($ch);
    curl_close($ch);
    usleep(200000); // Rate limit
    return json_decode($resp, true);
}

function getShopifyLocationGid($token, $shopurl, $name) {
    $q = "query { locations(first:20) { nodes { id name } } }";
    $r = send_graphql_request($token, $shopurl, $q);
    foreach ($r['data']['locations']['nodes'] ?? [] as $l)
        if ($l['name'] === $name) return $l['id'];
    return null;
}

function productQueryBySku_graphql($token, $shopurl, $sku) {
    $q = <<<'GRAPHQL'
query($sku:String!){productVariants(first:1,query:$sku){nodes{id sku inventoryItem{id} product{id}}}}
GRAPHQL;
    $v = ['sku' => "sku:'".str_replace("'", "\\'", $sku)."'"];
    $r = send_graphql_request($token, $shopurl, $q, $v);
    $n = $r['data']['productVariants']['nodes'][0] ?? null;
    return $n && $n['sku']=== $sku ? [
        'product_gid' => $n['product']['id'],
        'variant_gid' => $n['id'],
        'inventory_gid'=> $n['inventoryItem']['id']
    ] : null;
}

// CSERÉLD LE ERRE A TELJES FÜGGVÉNYRE:
// Ez a verzió már a 2024-04 API-nak megfelelő 'productOptions' argumentumot használja.
function productCreate_graphql($token, $shopurl, $input, $media = [], $productOptions = []) {
    $q = <<<'GRAPHQL'
mutation($input: ProductInput!, $media: [CreateMediaInput!]) {
  productCreate(input: $input, media: $media) {
    product { id title handle status }
    userErrors { field message }
  }
}
GRAPHQL;

    // Nest productOptions inside the input (as [OptionCreateInput!])
    if (!empty($productOptions)) {
        $input['productOptions'] = $productOptions;
    }

    return send_graphql_request($token, $shopurl, $q, [
        'input' => $input, 
        'media' => $media
    ]);
}


function productAddOptions_graphql($token, $shopurl, $productId, $options) {
    // Ez a függvény már valószínűleg nem használatos, de a teljesség kedvéért javítva.
    $q = <<<'GRAPHQL'
mutation($input: ProductInput!) {
  productUpdate(input: $input) {
    product { id options { name } }
    userErrors { field message }
  }
}
GRAPHQL;
    $input = [
        'id' => $productId,
        'options' => $options // Figyelem: A productUpdate más logikát használhat!
    ];
    return send_graphql_request($token, $shopurl, $q, ['input' => $input]);
}

function productVariantsBulkCreate_graphql($token, $shopurl, $productId, $variants) {
    $q = <<<'GRAPHQL'
mutation($productId:ID!,$variants:[ProductVariantsBulkInput!]!){
  productVariantsBulkCreate(productId:$productId,variants:$variants){
    productVariants{id sku inventoryItem{id}}
    userErrors{field message}
  }
}
GRAPHQL;
    return send_graphql_request($token, $shopurl, $q, ['productId' => $productId, 'variants' => $variants]);
}

function productVariantsBulkUpdate_graphql($token, $shopurl, $productId, $variants) {
    $q = <<<'GRAPHQL'
mutation($productId:ID!,$variants:[ProductVariantInput!]!){
  productVariantsBulkUpdate(productId:$productId,variants:$variants){
    productVariants{id price}
    userErrors{field message}
  }
}
GRAPHQL;
    return send_graphql_request($token, $shopurl, $q, ['productId' => $productId, 'variants' => $variants]);
}

function inventorySetQuantities_graphql($token, $shopurl, $sets) {
    $q = <<<'GRAPHQL'
mutation($sets:[InventorySetQuantityInput!]!){
  inventorySetQuantities(input:{setQuantities:$sets}){
    userErrors{field message}
  }
}
GRAPHQL;
    return send_graphql_request($token, $shopurl, $q, ['sets'=>$sets]);
}

function productUpdateStatus_graphql($token, $shopurl, $productId, $status) {
    $q = <<<'GRAPHQL'
mutation($id:ID!,$status:ProductStatus!){
  productUpdate(input:{id:$id,status:$status}){
    product{id status}
    userErrors{field message}
  }
}
GRAPHQL;
    return send_graphql_request($token, $shopurl, $q, ['id'=>$productId, 'status'=>$status]);
}

function productFullUpdate_graphql($token, $shopurl, $productId, $input) {
    $q = <<<'GRAPHQL'
mutation($input:ProductInput!){
  productUpdate(input:$input){
    product{id}
    userErrors{field message}
  }
}
GRAPHQL;
    $input['id'] = $productId;
    return send_graphql_request($token, $shopurl, $q, ['input' => $input]);
}

function productActivate_graphql($token, $shopurl, $productId) {
    // Ahelyett, hogy a productUpdateStatus-t hívnánk,
    // használjuk a hivatalos publishablePublish mutációt, ami megbízhatóbb
    $q = <<<'GRAPHQL'
mutation productPublish($id: ID!) {
  productPublish(id: $id, input: { publishDate: null, status: ACTIVE }) {
    product { id status }
    userErrors { field message }
  }
}
GRAPHQL;
    // A 'productUpdateStatus_graphql' helyett ezt hívjuk:
    return send_graphql_request($token, $shopurl, $q, ['id' => $productId]);
}

function productVariantFullUpdate_graphql($token, $shopurl, $input) {
    $q = <<<'GRAPHQL'
mutation($input:ProductVariantInput!){
  productVariantUpdate(input:$input){
    productVariant{id sku}
    userErrors{field message}
  }
}
GRAPHQL;
    return send_graphql_request($token, $shopurl, $q, ['input' => $input]);
}

function productVariantDelete_graphql($token, $shopurl, $variantId) {
    $q = <<<'GRAPHQL'
mutation($id:ID!){
  productVariantDelete(id:$id){
    deletedProductVariantId
    userErrors{field message}
  }
}
GRAPHQL;
    return send_graphql_request($token, $shopurl, $q, ['id' => $variantId]);
}

?>

