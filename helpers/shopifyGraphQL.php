<?php
// helpers/shopifyGraphQL.php (VÉGLEGES VERZIÓ V4 - JAVÍTOTT cURL/API VERZIÓVAL)

ini_set('max_execution_time', 0);
set_time_limit(0);

/**
 * 1. Alap cURL kérés küldése a Shopify GraphQL végpontra.
 * A legstabilabb '2024-04' API verziót használja, és kikapcsolja az SSL ellenőrzést.
 */
/**
 * VÉGLEGES HIBAKERESŐ VERZIÓ: Kiírja a nyers cURL hibaüzenetet és a választ.
 */
function send_graphql_request($token, $shopurl, $query, $variables = []) {
    $payload = json_encode(['query' => $query, 'variables' => $variables]);
    
    // Kényszerítsük a legstabilabb API verzióra: 2024-04
    $ch = curl_init("https://$shopurl/admin/api/2024-04/graphql.json");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false, // Kikapcsoljuk a tanúsítvány ellenőrzését
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "X-Shopify-Access-Token: $token"
        ],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload
    ]);
    
    $response = curl_exec($ch);
    $curl_error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    // ----------------------------------------
    // !!! DIAGNOSZTIKA !!!
    // ----------------------------------------
    echo "\n\n!!! SHOPIFY KOMMUNIKÁCIÓS HIBA !!!\n";
    echo "  HTTP KÓD: $http_code\n";
    echo "  cURL HIBA: $curl_error\n";
    echo "  NYERS VÁLASZ: " . substr($response, 0, 500) . "...\n";
    echo "!!! ---------------------------- !!!\n\n";
    // ----------------------------------------

    if (curl_errno($ch)) {
        curl_close($ch);
        return null; // Sikertelen cURL kapcsolat
    }
    
    curl_close($ch);
    return json_decode($response, true);
}

/**
 * 2. Lekérdezi egy Shopify Raktárhely (Location) GID-jét név alapján.
 * Ezt a függvényt VISSZA KELL ÁLLÍTANI a normál működésre, miután a hiba elhárult!
 */
function getShopifyLocationGid($token, $shopurl, $locationName) {
    // EZ A NORMÁL, NEM HIBAKERESŐ KÓD!
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

    $response = send_graphql_request($token, $shopurl, $query);

    if (isset($response['data']['locations']['nodes'])) {
        foreach ($response['data']['locations']['nodes'] as $location) {
            // Karakterre pontos egyezés
            if ($location['name'] === $locationName) {
                return $location['id'];
            }
        }
    }
    // Mivel a hiba itt van, a hívó szkript (indexnew.php) kezeli a null válasz miatti leállást.
    return null; 
}


// AZ ÖSSZES TÖBBI FÜGGVÉNY (productQueryBySku_graphql, productCreate_graphql, stb.)
// (Ezeket már nem ismétlem, de a GitHubra feltöltendő fájlnak tartalmaznia kell mindet!)
// ...

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

