<?php
// helpers/shopifyGraphQL.php (VÉGLEGES VERZIÓ V5 - KORRIGÁLT PAYLOAD)

ini_set('max_execution_time', 0);
set_time_limit(0);

/**
 * 1. Alap cURL kérés küldése a Shopify GraphQL végpontra.
 * JAVÍTVA: Nem küldi el a "variables" mezőt, ha az üres.
 */
function send_graphql_request($token, $shopurl, $query, $variables = []) {
    
    $data_to_send = ['query' => $query];
    // !!! A KRITIKUS JAVÍTÁS !!! Csak akkor küldjük el a 'variables' kulcsot, ha nem üres.
    if (!empty($variables)) {
        $data_to_send['variables'] = $variables;
    }
    
    $payload = json_encode($data_to_send);
    
    // Kényszerítsük a legstabilabb LTS verzióra: 2024-04
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
        CURLOPT_POSTFIELDS => $payload
    ]);
    
    $response = curl_exec($ch);
    $responseDecoded = json_decode($response, true);
    
    if (curl_errno($ch)) {
        curl_close($ch);
        return null;
    }
    
    curl_close($ch);
    
    // Alapvető hibaellenőrzés (a mi diagnosztikai kiírásunk)
    if (isset($responseDecoded['errors']) || (isset($responseDecoded['data']) && is_null($responseDecoded['data']) && !empty($payload))) {
        // Hagyjuk a hívó szkriptre, hogy eldöntse, mi legyen a null válasszal.
    }
    return $responseDecoded;
}


/**
 * 2. Lekérdezi egy Shopify Raktárhely (Location) GID-jét név alapján.
 * EZ A NORMÁL, MŰKÖDŐ VERZIÓ!
 */
function getShopifyLocationGid($token, $shopurl, $locationName) {
    $query = <<<'GRAPHQL'
query {
    locations(first: 20) {
        nodes {
            id
            name
        }
    }
}
GRAPHQL;

    // Nincs 'variables' paraméter átadva, így a send_graphql_request üresen hagyja a JSON-ban.
    $response = send_graphql_request($token, $shopurl, $query); 

    if (isset($response['data']['locations']['nodes'])) {
        foreach ($response['data']['locations']['nodes'] as $location) {
            if ($location['name'] === $locationName) {
                return $location['id'];
            }
        }
    }
    return null; 
}


// INNENTŐL A TÖBBI FÜGGVÉNY (productQueryBySku_graphql, productCreate_graphql, stb.)
// EZEKET TARTALMAZNIA KELL A FÁJLNAK.

function productQueryBySku_graphql($token, $shopurl, $sku) {
    $query = <<<'GRAPHQL'
query productVariantBySku($sku: String!) {
  productVariants(first: 1, query: $sku) {
    nodes {
      id
      sku
      inventoryItem {
        id
      }
      product {
        id
      }
    }
  }
}
GRAPHQL;
    $variables = ['sku' => "sku:'" . str_replace("'", "\\'", $sku) . "'"];
    $response = send_graphql_request($token, $shopurl, $query, $variables);
    
    if (isset($response['data']['productVariants']['nodes'][0])) {
        $variantNode = $response['data']['productVariants']['nodes'][0];
        if ($variantNode['sku'] === $sku) {
             return [
                'product_gid' => $variantNode['product']['id'],
                'variant_gid' => $variantNode['id'],
                'inventory_gid' => $variantNode['inventoryItem']['id']
            ];
        }
    }
    return null;
}

function productCreate_graphql($token, $shopurl, $variables) {
    $query = <<<'GRAPHQL'
mutation productCreate($input: ProductInput!) {
    productCreate(input: $input) {
        product {
            id
            variants(first: 250) { 
                nodes {
                    id
                    sku
                    inventoryItem {
                        id
                    }
                }
            }
        }
        userErrors {
            field
            message
        }
    }
}
GRAPHQL;
    return send_graphql_request($token, $shopurl, $query, $variables);
}

function productUpdateStatus_graphql($token, $shopurl, $product_gid, $status) {
    $query = <<<'GRAPHQL'
mutation productUpdateStatus($productId: ID!, $status: ProductStatus!) {
    productUpdate(input: {id: $productId, status: $status}) {
        product { id status }
        userErrors { field message }
    }
}
GRAPHQL;
    $variables = [
        'productId' => $product_gid, 
        'status' => $status
    ];
    return send_graphql_request($token, $shopurl, $query, $variables);
}

function productFullUpdate_graphql($token, $shopurl, $product_gid, $input) {
    $query = <<<'GRAPHQL'
mutation productUpdate($input: ProductInput!) {
    productUpdate(input: $input) {
        product { id title }
        userErrors { field message }
    }
}
GRAPHQL;
    $variables = ['input' => $input];
    return send_graphql_request($token, $shopurl, $query, $variables);
}


function productVariantsBulkUpdate_graphql($token, $shopurl, $product_gid, $variants) {
    $query = <<<'GRAPHQL'
mutation productVariantsBulkUpdate($productId: ID!, $variants: [ProductVariantsBulkInput!]!) {
    productVariantsBulkUpdate(productId: $productId, variants: $variants) {
        userErrors { field message }
        job {
          id
          status
        }
    }
}
GRAPHQL;

    $variables = [
        'productId' => $product_gid,
        'variants' => $variants
    ];
    return send_graphql_request($token, $shopurl, $query, $variables);
}


function inventorySetQuantities_graphql($token, $shopurl, $sets) {
    $query = <<<'GRAPHQL'
mutation inventorySetQuantities($sets: [InventorySetQuantityInput!]!) {
    inventorySetQuantities(input: { setQuantities: $sets }) {
        inventoryAdjustmentGroup {
            createdAt
            reason
        }
        userErrors {
            field
            message
        }
    }
}
GRAPHQL;
    
    $variables = ['sets' => $sets];
    
    return send_graphql_request($token, $shopurl, $query, $variables);
}
