<?php
// helpers/shopifyGraphQL.php (V7 – 2025-10 + HIÁNYZÓ FÜGGVÉNYEK)

ini_set('max_execution_time', 0);
set_time_limit(0);

function send_graphql_request($token, $shopurl, $query, $variables = []) {
    $data = ['query' => $query];
    if (!empty($variables)) $data['variables'] = $variables;

    $ch = curl_init("https://$shopurl/admin/api/2025-10/graphql.json");
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
    usleep(200000);
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

function productCreate_graphql($token, $shopurl, $input, $media = []) {
    $q = <<<'GRAPHQL'
mutation($input:ProductInput!,$media:[CreateMediaInput!]){
  productCreate(input:$input,media:$media){
    product{id title handle status}
    userErrors{field message}
  }
}
GRAPHQL;
    return send_graphql_request($token, $shopurl, $q, ['input' => $input, 'media' => $media]);
}

function productAddOptions_graphql($token, $shopurl, $productId, $options) {
    $q = <<<'GRAPHQL'
mutation($id:ID!,$options:[String!]!){
  productUpdate(input:{id:$id,options:$options}){
    product{id}
    userErrors{field message}
  }
}
GRAPHQL;
    return send_graphql_request($token, $shopurl, $q, ['id' => $productId, 'options' => $options]);
}

function productVariantsBulkCreate_graphql($token, $shopurl, $productId, $variants) {
    $q = <<<'GRAPHQL'
mutation($productId:ID!,$variants:[ProductVariantInput!]!){
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
    productUpdateStatus_graphql($token, $shopurl, $productId, 'ACTIVE');
}

// ... (a fájl többi része) ...
// ÚJ FÜGGVÉNY a V24-hez
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

// ÚJ FÜGGVÉNY a V24-hez
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

