<?php
// helpers/shopifyGraphQL.php (JAVÍTOTT VERZIÓ: 2025-10 API-val, új termékmodell, timeout-ok)

ini_set('max_execution_time', 0);
set_time_limit(0);

function send_graphql_request($token, $shopurl, $query, $variables = []) {
    $data = ['query' => $query];
    if (!empty($variables)) $data['variables'] = $variables;

    // Frissített API verzió
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
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_CONNECTTIMEOUT => 10,  // Kapcsolódási timeout
        CURLOPT_TIMEOUT => 30          // Teljes kérés timeout
    ]);

    $resp = curl_exec($ch);
    if ($resp === false) {
        echo "CURL HIBA: " . curl_error($ch) . "\n";
    }
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

// Új modell: Termék létrehozása opciók nélkül
function productCreate_graphql($token, $shopurl, $input, $media = []) {
    $q = <<<'GRAPHQL'
mutation($input: ProductInput!, $media: [CreateMediaInput!]) {
  productCreate(input: $input, media: $media) {
    product { id title handle status }
    userErrors { field message }
  }
}
GRAPHQL;

    return send_graphql_request($token, $shopurl, $q, [
        'input' => $input, 
        'media' => $media
    ]);
}

// Új: OPCIÓK hozzáadása külön mutációval (új modell)
function productOptionsCreate_graphql($token, $shopurl, $productId, $options) {
    $q = <<<'GRAPHQL'
mutation($productId: ID!, $productOptions: [ProductOptionCreateInput!]!) {
  productOptionsCreate(productId: $productId, productOptions: $productOptions) {
    productOptions { id name values }
    userErrors { field message }
  }
}
GRAPHQL;
    return send_graphql_request($token, $shopurl, $q, ['productId' => $productId, 'productOptions' => $options]);
}

function productVariantsBulkCreate_graphql($token, $shopurl, $productId, $variants) {
    $q = <<<'GRAPHQL'
mutation($id:ID!,$variants:[ProductVariantInput!]!){
  productVariantsBulkCreate(productId:$id,variants:$variants){
    productVariants{id sku inventoryItem{id}}
    userErrors{field message}
  }
}
GRAPHQL;
    return send_graphql_request($token, $shopurl, $q, ['id' => $productId, 'variants' => $variants]);
}

function productVariantsBulkUpdate_graphql($token, $shopurl, $productId, $variants) {
    $q = <<<'GRAPHQL'
mutation($id:ID!,$variants:[ProductVariantInput!]!){
  productVariantsBulkUpdate(productId:$id,variants:$variants){
    productVariants{id price}
    userErrors{field message}
  }
}
GRAPHQL;
    return send_graphql_request($token, $shopurl, $q, ['id' => $productId, 'variants' => $variants]);
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
    $q = <<<'GRAPHQL'
mutation productPublish($id: ID!) {
  productPublish(id: $id, input: { publishDate: null, status: ACTIVE }) {
    product { id status }
    userErrors { field message }
  }
}
GRAPHQL;
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
