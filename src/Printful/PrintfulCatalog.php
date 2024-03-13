<?php

namespace Admin\Printful;

require 'vendor/autoload.php';

use GuzzleHttp\Client;

class PrintfulCatalog
{
    private $cache;

    public function __construct(CacheInterface $cache)
    {
        // Assign the provided cache object to the class property
        $this->cache = $cache;
    }
    // Fetch product and size data from the Printful API
    private function fetchDataFromApi(int $id, string $size)
    {
        // init Guzzle client
        $client = new Client();
        $productApi = "https://api.printful.com/products/{$id}";

        // Make a GET request to the API
        $response = $client->get($productApi);

        // Decode the JSON response
        $productData = json_decode($response->getBody(), true);

        // var_dump($productData);


        // return the 
        return $this->formatData($productData, $id, $size);
    }

// format the api data
private function formatData(array $productData, int $id, string $size)
{
    // Extract product and size information
    $product = $this->extractProduct($productData, $id);
    $sizeData = $this->extractSize($product, $size);

    // Check if product information is available
    if (isset($product['id'], $product['title'], $product['description'])) {
        // Combine product and size data
        $formattedData = ['product' => $product, 'size' => $sizeData];

        // Cache the formatted data for 5 minutes
        $this->cacheData($formattedData, $id, $size);

        return $formattedData;
    } else {
        // Handle the case when product information is not available
        echo "Product information is not available for ID $id.";

        // You might want to return or handle this case differently based on your requirements
        return [];
    }
}


private function extractProduct(array $productData, int $id)
{
    // Check if the 'catalog_product_id' key exists
    if (isset($productData['result']['product']['id']) && $productData['result']['product']['id'] === $id) {
        $product = $productData['result']['product'];

        // Extract relevant product details
        $extractedProduct = [
            'id' => $product['id'] ?? null,
            'title' => $product['title'] ?? null,
            'description' => $product['description'] ?? null,
        ];

        // Print the product information for debugging
        print_r($extractedProduct);

        return $extractedProduct;
    } else {
        // Print a message indicating that the product was not found
        echo "Product information is not available for ID $id.";

        // Print the entire API response for further debugging
        print_r($productData);
    }

    return [];
}



    
 
// Extract size information from the product data
private function extractSize(array $product, string $size)
{
    // Check if 'size_tables' key exists and is an array
    $sizeTables = $product["size_tables"] ?? [];


  
    // print_r($sizeTables);

    // Loop through each 'size_tables' entry
    foreach ($sizeTables as $sizeTable) {
        // Check if 'sizes' key exists within the current 'size_table' entry
        $sizes = $sizeTable['sizes'] ?? [];

  
        // Loop through each size in the 'sizes' array
        foreach ($sizes as $sizeData) {
            // Check if 'size' key exists in the current $sizeData
            if (isset($sizeData['size']) && $sizeData['size'] === $size) {
                return [
                    'type' => $sizeData['type'] ?? null,
                    'unit' => $sizeData['unit'] ?? null,
                    'description' => $sizeData['description'] ?? null,
                    'measurements' => [
                        'type_label' => $sizeData['measurements']['type_label'] ?? null,
                        'value' => $sizeData['measurements']['values'][$size] ?? null,
                    ],
                ];
            }
        }

    }

    return [];
}





    // Cache the formatted data for 5 minutes
    private function cacheData(array $formattedData, int $id, string $size)
    {
        // Generate a unique cache key
        $cacheKey = "product_{$id}_size_{$size}";

        // Store the formatted data in the cache
        $this->cache->set($cacheKey, $formattedData, 300);
    }




    // Retrieve product and size information
    public function getProductAndSize(int $id, string $size)
    {
        // Generate a unique cache key based on the product ID and size
        $cacheKey = "product_{$id}_size_{$size}";

        // Check if the data is in the cache
        $cachedData = $this->cache->get($cacheKey);

        if ($cachedData !== null) {
            // If data is in the cache, return it
            return $cachedData;
        }

        // Fetch data from API if not in the cache
        $apiData = $this->fetchDataFromApi($id, $size);

        // Cache the data for 5 minutes
        $this->cache->set($cacheKey, $apiData, 300);
        // var_dump($apiData);
        return $apiData;
    }
}
