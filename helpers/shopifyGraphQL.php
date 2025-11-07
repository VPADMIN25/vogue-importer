<?php
// helpers/shopifyGraphQL.php (VÉGLEGES VERZIÓ V3)
// A Shopify GraphQL API-hoz szükséges összes függvényt tartalmazza.

ini_set('max_execution_time', 0);
set_time_limit(0);

/**
 * Alap cURL kérés küldése a Shopify GraphQL végpontra.
 */
function send_graphql_request($token, $shopurl, $query, $variables = []) {
    $payload = json_encode(['query' => $query, 'variables' => $variables]);
    
    $ch = curl_init("https://$shopurl/admin/api/2024-10/graphql.json");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
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
        // Hiba kezelése (pl. hálózati probléma)
        // echo "cURL Hiba: " . curl_error($ch) . "\n";
        curl_close($ch);
        return null;
    }
    
    curl_close($ch);
    return $responseDecoded;
}

/**
 * Lekérdezi egy Shopify Raktárhely (Location) GID-jét név alapján.
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

/**
 * Lekérdezi a Termék, Variáns és Inventory Item GID-jeit egy SKU (a mi Generált SKU-nk) alapján.
 * EZ FONTOS AZ ÖRÖKBEFOGADÁSHOZ!
 */
function productQueryBySku_graphql($token, $shopurl, $sku) {
    // A query a `productVariants` kollekcióban keres SKU-ra.
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

    // Fontos, hogy az SKU-t stringként és pontos egyezésre kényszerítve adjuk át a lekérdezésben.
    // Így csak a teljes SKU-ra keres, nem csak egy részére.
    $variables = ['sku' => "sku:'" . str_replace("'", "\\'", $sku) . "'"];
    
    $response = send_graphql_request($token, $shopurl, $query, $variables);
    
    if (isset($response['data']['productVariants']['nodes'][0])) {
        $variantNode = $response['data']['productVariants']['nodes'][0];
        // Ellenőrizzük, hogy a talált SKU pontosan egyezik-e
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

/**
 * Új termék létrehozása (index2new.php használja)
 */
function productCreate_graphql($token, $shopurl, $variables) {
    $query = <<<'GRAPHQL'
mutation productCreate($input: ProductInput!) {
    productCreate(input: $input) {
        product {
            id
            variants(first: 250) { // Variánsok GID-jének visszakérése
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

/**
 * Termék státuszának módosítása (ARCHIVED / ACTIVE)
 */
function productUpdateStatus_graphql($token, $shopurl, $product_gid, $status) {
    // Termék GID-et vár (pl. 'gid://shopify/Product/123456789')
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

/**
 * Teljes termék felülírása (Cím, Leírás, Vendor, Képek, Status)
 * Használva a needs_update=10 (Örökbefogadás/Javítás) esetén.
 */
function productFullUpdate_graphql($token, $shopurl, $product_gid, $input) {
    $query = <<<'GRAPHQL'
mutation productUpdate($input: ProductInput!) {
    productUpdate(input: $input) {
        product { id title }
        userErrors { field message }
    }
}
GRAPHQL;
    // A $product_gid-et már beletettük az $input 'id' mezőjébe a hívó szkriptben (index3new.php)
    $variables = ['input' => $input];
    return send_graphql_request($token, $shopurl, $query, $variables);
}


/**
 * Több Variáns Árának Tömeges Frissítése (Bulk Update)
 * A needs_update=1 és 10 esetén használatos.
 */
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


/**
 * Több Inventory Item Készletének Tömeges Beállítása
 * A needs_update=1 és 10 esetén használatos.
 */
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
?>
