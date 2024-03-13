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
        
        $response = $client->get($productApi);
        // Decode the JSON response
        $productData = json_decode($response->getBody(), true);

        // var_dump($productData);


        // return the formatted data
        return $this->formatData($productData, $id, $size);
    }


// format the api data
// Format product and size data into a structured array
private function formatData(array $productData, int $id, string $size)
{
    // Extract product and size information
    $product = $this->extractProduct($productData, $id);
    $sizeData = $this->extractSize($product, $size);
    $sizeTableData = $this->extractSizeTable($productData);

    // Combine product, size, and size table data
    $formattedData = [
        'product' => $product,
        'size' => $sizeData,
        'size_table' => $sizeTableData,
    ];

    // Cache the formatted data for 5 minutes
    $this->cacheData($formattedData, $id, $size);

    var_dump($formattedData);
    return $formattedData;
}

private function extractSizeTable(array $productData)
{
    // Check if 'size_tables' key exists and is an array
    if (isset($productData['result']['size_tables']) && is_array($productData['result']['size_tables'])) {
        $sizeTable = $productData['result']['size_tables'][0]; // Assuming there's only one size table

        // Extract relevant size table details
        $extractedSizeTable = [
            'type' => $sizeTable['type'] ?? null,
            'unit' => $sizeTable['unit'] ?? null,
            'description' => $sizeTable['description'] ?? null,
            'measurements' => $sizeTable['measurements'] ?? [],
        ];

        // Print the size table information for debugging
        print_r($extractedSizeTable);

        return $extractedSizeTable;
    } else {
        // Print a message indicating that the size table data is not available
        echo "Size table data is not available in API response.";

        // Print the entire API response for further debugging
        print_r($productData);
    }

    return [];
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
        // print_r($extractedProduct);

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
