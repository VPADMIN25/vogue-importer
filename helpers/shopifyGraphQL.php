<?php

function addShopifyProduct_graphql($token, $shopurl, $variables){
    $query = <<<GRAPHQL
    mutation productCreate(\$input: ProductInput, \$media: [CreateMediaInput!], \$product: ProductCreateInput) {
        productCreate(input: \$input, media: \$media, product: \$product) {
        product {
            id
            variants(first: 10) {
            nodes {
                id
                barcode
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
    $data = [
        'query' => $query,
        'variables' => $variables,
    ];
    
    $url = "https://$shopurl/admin/api/2024-10/graphql.json";
    $headers = [
        'Content-Type: application/json',
        'X-Shopify-Access-Token: ' . $token,
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES128-GCM-SHA256');
    
    $response = curl_exec($ch);
    sleep(1);
    
    curl_close($ch);
    $responseDecoded = json_decode($response, true);

    // Check if there are user errors in the response
    if (!empty($responseDecoded['data']['productCreate']['userErrors'])) {
        $userErrors = $responseDecoded['data']['productCreate']['userErrors'];
    
        // Debug: Print the user errors
        print_r($userErrors);
    
        // Loop through each error
        foreach ($userErrors as $error) {
            // Check if the error message matches the specific error
            if (
                isset($error['message']) &&
                $error['message'] === 'The file is not supported on trial accounts. Select a plan to upload this file.'
            ) {
                // Check if 'media' exists in $variables
                if (isset($variables['media'])) {
                    // Remove the 'media' key
                    unset($variables['media']); // Directly modify the $variables array
    
                    // Debug: Print the updated $variables
    
                    // Call the function again with updated variables
                    $response  =  addShopifyProduct_graphql($token, $shopurl, $variables);
                    //$responseDecoded = json_decode($response, true);
                    print_r($responseDecoded);
                    return  $response ;
                }
            }
        }
    }
    else{
    
         print_r($responseDecoded);
    
      return $responseDecoded;
    }
}

function updateShopifyVariant_graphql($token, $shopurl, $variables){
    global $conn;
    $query = <<<'GRAPHQL'
    mutation productVariantsBulkUpdate($media: [CreateMediaInput!],$productId: ID!, $variants: [ProductVariantsBulkInput!]!) {
    productVariantsBulkUpdate( media: $media,productId: $productId, variants: $variants) {
        product {
        id
        }
        productVariants {
        id
        metafields(first: 2) {
            edges {
            node {
                namespace
                key
                value
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

    $payload = json_encode([
        "query" => $query,
        "variables" => $variables,
    ]);
    $url = "https://$shopurl/admin/api/2024-10/graphql.json";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "X-Shopify-Access-Token: $token",
    ]);
    $response = curl_exec($ch);
    $responseDecoded = json_decode($response, true);
    print_r( $responseDecoded );

    if (curl_errno($ch)) {
        echo "cURL error: " . curl_error($ch);
    } else {
        if(!empty($responseDecoded['data']['productVariantsBulkUpdate']['userErrors'])){
            return $responseDecoded;
          }
         return $responseDecoded;
    }
    // Close cURL
    curl_close($ch);
}
function createShopifyVaraint_graphql($shopurl,$token,$variables){
    addlog(json_encode($variables),"INFO");
    $query = <<<GRAPHQL
    mutation productVariantsBulkCreate(\$media: [CreateMediaInput!],\$productId: ID!, \$variants: [ProductVariantsBulkInput!]!) {
    productVariantsBulkCreate(media: \$media,productId: \$productId, variants: \$variants) {
        userErrors {
        field
        message
        }
        product {
        id
        }
        productVariants {
        id
        title
        barcode
        inventoryItem{
            id
        }
        selectedOptions {
            name
            value
        }
        }
    }
    }
    GRAPHQL;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://$shopurl/admin/api/2024-10/graphql.json"); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "X-Shopify-Access-Token: $token"
    ]);
    $data = json_encode([
        "query" => $query,
        "variables" => $variables
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    } else {
        $result = json_decode($response, true);
        addlog("createShopifyVaraint response :","INFO");
        addlog(json_encode($result),"INFO");
        if(!empty($result['data']['productVariantsBulkCreate']['userErrors'])){
            return false;
        }
        return $result;
    }
    return $result;


    curl_close($ch);
}
function updateShopifyInventory_graphql($token, $shopurl, $variables){
    global $conn;
    $query = <<<QUERY
        mutation InventorySet(\$input: InventorySetQuantitiesInput!) {
          inventorySetQuantities(input: \$input) {
            inventoryAdjustmentGroup {
              createdAt
              reason
              changes {
                name
                delta
              }
            }
            userErrors {
              field
              message
            }
          }
        }
        QUERY;			
        $payload = json_encode([
            'query' => $query,
            'variables' => $variables
        ]);
        $ch = curl_init("https://$shopurl/admin/api/2024-10/graphql.json");
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "X-Shopify-Access-Token: $token"
        ]);
        
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        
        $response = curl_exec($ch);
        
        $responseDecoded = json_decode($response,true);
        print_r($responseDecoded);

        if(!empty($responseDecoded['data']['inventorySetQuantities']['userErrors'])){
            return false;
        }else{
    
            return true;
        }
}

?>