<?php
// helpers/shopifyGraphQL.php (VÉGLEGES V6 – 2024-04 KOMPATIBILIS)

ini_set('max_execution_time', 0);
set_time_limit(0);

function send_graphql_request($token, $shopurl, $query, $variables = []) {
    $data = ['query' => $query];
    if (!empty($variables)) $data['variables'] = $variables;

    $ch = curl_init("https://$shopurl/admin/api/2024-04/graphql.json");
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
    return json_decode($resp, true);
}

/* —— 1. RAKTÁRHELY —— */
function getShopifyLocationGid($token, $shopurl, $name) {
    $q = "query { locations(first:20) { nodes { id name } } }";
    $r = send_graphql_request($token, $shopurl, $q);
    foreach ($r['data']['locations']['nodes'] ?? [] as $l)
        if ($l['name'] === $name) return $l['id'];
    return null;
}

/* —— 2. TERMÉK KERESÉSE SKU ALAPJÁN —— */
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

/* —— 3. CSAK A TERMÉK VÁZ + KÉPEK (2024-04) —— */
function productCreate_graphql($token, $shopurl, $input, $media = []) {
    $q = <<<'GRAPHQL'
mutation($input:ProductInput!,$media:[CreateMediaInput!]){
  productCreate(input:$input,media:$media){
    product{id title handle status}
    userErrors{field message}
  }
}
GRAPHQL;
    return send_graphql_request($token, $shopurl, $q, [
        'input' => $input,
        'media' => $media
    ]);
}

/* —— 4. OPCIÓK HOZZÁADÁSA —— */
function productAddOptions_graphql($token, $shopurl, $productId, $options) {
    $q = <<<'GRAPHQL'
mutation($id:ID!,$options:[String!]!){
  productUpdate(input:{id:$id,options:$options}){
    product{id options{name values}}
    userErrors{field message}
  }
}
GRAPHQL;
    return send_graphql_request($token, $shopurl, $q, [
        'id' => $productId,
        'options' => $options
    ]);
}

/* —— 5. VARIÁNTOK TÖMEGES HOZZÁADÁSA —— */
function productVariantsBulkCreate_graphql($token, $shopurl, $productId, $variants) {
    $q = <<<'GRAPHQL'
mutation($productId:ID!,$variants:[ProductVariantInput!]!){
  productVariantsBulkCreate(productId:$productId,variants:$variants){
    productVariants{id sku inventoryItem{id}}
    userErrors{field message}
  }
}
GRAPHQL;
    return send_graphql_request($token, $shopurl, $q, [
        'productId' => $productId,
        'variants' => $variants
    ]);
}

/* —— 6. KÉSZLET BEÁLLÍTÁSA —— */
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

/* —— 7. STÁTUSZ VÁLTÁS —— */
function productActivate_graphql($token, $shopurl, $productId) {
    $q = <<<'GRAPHQL'
mutation($id:ID!){
  productUpdate(input:{id:$id,status:ACTIVE}){
    product{id status}
    userErrors{field message}
  }
}
GRAPHQL;
    return send_graphql_request($token, $shopurl, $q, ['id'=>$productId]);
}
?>
