<?php
// helpers/shopifyGraphQL.php (V8 – 2025-11 – TELJESEN JAVÍTOTT, 2024-10+ API KOMPATIBILIS)

ini_set('max_execution_time', 0);
set_time_limit(0);

// ---------------------------
// ALAP GraphQL KÉRÉS
// ---------------------------
function send_graphql_request($token, $shopurl, $query, $variables = []) {
    $data = ['query' => $query];
    if (!empty($variables)) {
        $data['variables'] = $variables;
    }

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
    usleep(200000); // Rate limit védelem (5 req/s)
    return json_decode($resp, true);
}

// ---------------------------
// HELYSEG (LOCATION) LEKÉRDEZÉS
// ---------------------------
function getShopifyLocationGid($token, $shopurl, $name) {
    $q = "query { locations(first:20) { nodes { id name } } }";
    $r = send_graphql_request($token, $shopurl, $q);
    foreach ($r['data']['locations']['nodes'] ?? [] as $l) {
        if ($l['name'] === $name) return $l['id'];
    }
    return null;
}

// ---------------------------
// TERMÉK KERESÉSE SKU ALAPJÁN
// ---------------------------
function productQueryBySku_graphql($token, $shopurl, $sku) {
    $q = <<<'GRAPHQL'
query($sku: String!) {
  productVariants(first: 1, query: $sku) {
    nodes {
      id
      sku
      inventoryItem { id }
      product { id }
    }
  }
}
GRAPHQL;

    $v = ['sku' => "sku:'" . str_replace("'", "\\'", $sku) . "'"];
    $r = send_graphql_request($token, $shopurl, $q, $v);
    $n = $r['data']['productVariants']['nodes'][0] ?? null;

    return $n && $n['sku'] === $sku ? [
        'product_gid' => $n['product']['id'],
        'variant_gid' => $n['id'],
        'inventory_gid' => $n['inventoryItem']['id']
    ] : null;
}

// ---------------------------
// ÚJ TERMÉK LÉTREHOZÁSA (HELYES VERZIÓ)
// ---------------------------
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
// ---------------------------
// TERMÉK OPCIÓK CSERÉJE (helyettesíti a régi productUpdate(options:))
// ---------------------------
function productReplaceOptions_graphql($token, $shopurl, $productId, $options) {
    $q = <<<'GRAPHQL'
mutation($productId: ID!, $options: [ProductOptionInput!]!) {
  productOptionsReplace(productId: $productId, options: $options) {
    product {
      id
      options {
        id
        name
        values
      }
    }
    userErrors { field message }
  }
}
GRAPHQL;

    $vars = [
        'productId' => $productId,
        'options' => array_map(fn($opt) => ['name' => $opt], $options)
    ];

    return send_graphql_request($token, $shopurl, $q, $vars);
}

// ---------------------------
// VARIÁNSOK TÖMEGES LÉTREHOZÁSA
// ---------------------------
function productVariantsBulkCreate_graphql($token, $shopurl, $productId, $variants) {
    $q = <<<'GRAPHQL'
mutation($productId: ID!, $variants: [ProductVariantsBulkInput!]!) {
  productVariantsBulkCreate(productId: $productId, variants: $variants) {
    productVariants {
      id
      sku
      inventoryItem { id }
    }
    userErrors { field message }
  }
}
GRAPHQL;

    return send_graphql_request($token, $shopurl, $q, [
        'productId' => $productId,
        'variants' => $variants
    ]);
}

// ---------------------------
// VARIÁNSOK TÖMEGES FRISSÍTÉSE
// ---------------------------
function productVariantsBulkUpdate_graphql($token, $shopurl, $productId, $variants) {
    $q = <<<'GRAPHQL'
mutation($productId: ID!, $variants: [ProductVariantInput!]!) {
  productVariantsBulkUpdate(productId: $productId, variants: $variants) {
    productVariants {
      id
      price
    }
    userErrors { field message }
  }
}
GRAPHQL;

    return send_graphql_request($token, $shopurl, $q, [
        'productId' => $productId,
        'variants' => $variants
    ]);
}

// ---------------------------
// KÉSZLET FRISSÍTÉSE
// ---------------------------
function inventorySetQuantities_graphql($token, $shopurl, $sets) {
    $q = <<<'GRAPHQL'
mutation($sets: [InventorySetQuantityInput!]!) {
  inventorySetQuantities(input: { setQuantities: $sets }) {
    userErrors { field message }
  }
}
GRAPHQL;

    return send_graphql_request($token, $shopurl, $q, ['sets' => $sets]);
}

// ---------------------------
// TERMÉK ÁLLAPOT FRISSÍTÉSE (pl. ACTIVE)
// ---------------------------
function productUpdateStatus_graphql($token, $shopurl, $productId, $status) {
    $q = <<<'GRAPHQL'
mutation($id: ID!, $status: ProductStatus!) {
  productUpdate(input: { id: $id, status: $status }) {
    product { id status }
    userErrors { field message }
  }
}
GRAPHQL;

    return send_graphql_request($token, $shopurl, $q, [
        'id' => $productId,
        'status' => $status
    ]);
}

// ---------------------------
// TERMÉK TELJES FRISSÍTÉSE (NINCS options!)
// ---------------------------
function productFullUpdate_graphql($token, $shopurl, $productId, $input) {
    $input['id'] = $productId;

    $q = <<<'GRAPHQL'
mutation($input: ProductInput!) {
  productUpdate(input: $input) {
    product {
      id
      title
      handle
      status
    }
    userErrors { field message }
  }
}
GRAPHQL;

    return send_graphql_request($token, $shopurl, $q, ['input' => $input]);
}

// ---------------------------
// TERMÉK AKTIVÁLÁSA
// ---------------------------
function productActivate_graphql($token, $shopurl, $productId) {
    return productUpdateStatus_graphql($token, $shopurl, $productId, 'ACTIVE');
}

// ---------------------------
// VARIÁNS TELJES FRISSÍTÉSE
// ---------------------------
function productVariantFullUpdate_graphql($token, $shopurl, $input) {
    $q = <<<'GRAPHQL'
mutation($input: ProductVariantInput!) {
  productVariantUpdate(input: $input) {
    productVariant { id sku }
    userErrors { field message }
  }
}
GRAPHQL;

    return send_graphql_request($token, $shopurl, $q, ['input' => $input]);
}

// ---------------------------
// VARIÁNS TÖRLÉSE
// ---------------------------
function productVariantDelete_graphql($token, $shopurl, $variantId) {
    $q = <<<'GRAPHQL'
mutation($id: ID!) {
  productVariantDelete(id: $id) {
    deletedProductVariantId
    userErrors { field message }
  }
}
GRAPHQL;

    return send_graphql_request($token, $shopurl, $q, ['id' => $variantId]);
}

// ---------------------------
// SEGÉDFÜGGVÉNY: VARIÁNSOK TÖMEGES ÁRFRISSÍTÉSE
// ---------------------------
function updateVariantPrices_graphql($token, $shopurl, $updates) {
    // $updates: [['id' => 'gid://...', 'price' => '29.99'], ...]
    $variants = array_map(fn($u) => ['id' => $u['id'], 'price' => $u['price']], $updates);
    $productId = $updates[0]['productId'] ?? null;
    if (!$productId) return ['error' => 'productId required'];

    return productVariantsBulkUpdate_graphql($token, $shopurl, $productId, $variants);
}

// ---------------------------
// SEGÉDFÜGGVÉNY: KÉSZLET BEÁLLÍTÁSA VARIÁNSRA
// ---------------------------
function setVariantInventory_graphql($token, $shopurl, $inventoryItemId, $locationId, $quantity) {
    $sets = [[
        'inventoryItemId' => $inventoryItemId,
        'locationId' => $locationId,
        'quantity' => $quantity
    ]];
    return inventorySetQuantities_graphql($token, $shopurl, $sets);
}

?>



