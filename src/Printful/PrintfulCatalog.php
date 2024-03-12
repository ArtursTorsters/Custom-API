<?php
namespace Admin\Printful;
require 'vendor/autoload.php';

use GuzzleHttp\Client;

class PrintfulCatalog
{
    private $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function getProductAndSize(int $productId, string $size)
    {
        // Generate a unique cache key based on the product ID and size
        $cacheKey = "product_{$productId}_size_{$size}";

        // Check if the data is in the cache
        $cachedData = $this->cache->get($cacheKey);

        if ($cachedData !== null) {
            // If data is in the cache, return it
            return $cachedData;
        }

        // fetch data from api 
        $apiData = $this->fetchDataFromApi($productId, $size);

        // Cache the fetched data for five minutes
        $this->cache->set($cacheKey, $apiData, 300);

        return $apiData;
    }

    private function fetchDataFromApi(int $productId, string $size)
    {
        $client = new Client();
    
        // Construct the API endpoint URL for the product information
        $productApi = "https://api.printful.com/products/438/sizes";
    
        // Make a GET request to the Printful Catalog API to fetch product information
        $response = $client->get($productApi);
    
        // Extract the JSON response body and decode it into an associative array
        $productData = json_decode($response->getBody(), true);
    
        // Add debug information to see the raw API response
        var_dump($productData);
    
        // Check if the product data is valid
        if (isset($productData['result']['id'], $productData['result']['title'], $productData['result']['description'])) {
            // Continue with processing the data
            return [
                'id' => $productData['result']['id'],
                'title' => $productData['result']['title'],
                'description' => $productData['result']['description'],
                // Add additional information related to the specified size if needed
                'size' => $size,
            ];
        } else {
            // Handle the case where required keys are missing
            return null;
        }
    }
    
    
}
