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

    private function fetchDataFromApi(int $id, string $size)
    {
        // init Guzzle client
        $client = new Client();
        $productApi = "https://api.printful.com/products/{$id}";
        $sizeTableApi = "https://api.printful.com/products/{$id}/sizes";

        // Make a GET request to the API for product data
        $productResponse = $client->get($productApi);

        // Make a GET request to the API for size table data
        $sizeTableResponse = $client->get($sizeTableApi);

        // Decode the JSON responses
        $productData = json_decode($productResponse->getBody(), true);
        $sizeTableData = json_decode($sizeTableResponse->getBody(), true);



        // return the formatted data
        return $this->formatData($productData, $sizeTableData, $id, $size);
    }


    // Format the API data
    private function formatData(array $productData, array $sizeTableData, int $id, string $size)
    {
        // Extract product and size information
        $product = $this->extractProduct($productData, $id);

        // Extract size table information
        $sizeTable = $this->extractSizeTable($sizeTableData, $size);

        // Check if product information is available
        if (isset($product['id'], $product['title'], $product['description'])) {
            // Combine product, size, and size table data
            $formattedData = ['product' => $product, 'size' => $size, 'size_table' => $sizeTable];

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

    // single product
    private function extractProduct(array $productData, int $id)
    {
        // Check if the product key exists
        if (isset($productData['result']['product']['id'])) {
            $product = $productData['result']['product'];

            // Extract relevant product details
            return [
                'id' => $product['id'] ?? null,
                'title' => $product['title'] ?? null,
                'description' => $product['description'] ?? null,
            ];
        }
        return null;
    }





    // multi sizes for
    private function extractSizeTable(array $sizeTableData, string $size)
    {
        $sizeTables = $sizeTableData['result']['size_tables'];
        foreach ($sizeTables as $sizeTable) {
            return [
                'type' => $sizeTable['type'],
                'unit' => $sizeTable['unit'],
                'description' => $sizeTable['description'],
                'measurements' => $this->extractSizeTableMeasurements($sizeTable, $size),
            ];
        }
        return null;
    }

    private function extractSizeTableMeasurements(array $sizeTable, string $size)
    {
        $measurements = [];

        foreach ($sizeTable['measurements'] as $measurement) {
   // Extract the requested size value based on the type_label
   $typeLabel = $measurement['type_label'];
   $value = '';


   $measurements[] = [
       'type_label' => $typeLabel,
       'value' => $value,
   ];
        }

        return $measurements;
    }


    private function cacheData(array $formattedData, int $id, string $size)
    {
        // Generate a unique cache key
        $cacheKey = "product_{$id}_size_{$size}";

        // Store the formatted data in the cache
        $this->cache->set($cacheKey, $formattedData, 300);
    }


    // Retrieve product, size, and size table information
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

        return $apiData;
    }
}
