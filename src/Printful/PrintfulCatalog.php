<?php

namespace Admin\Printful;

use Admin\Printful\api\Api;
use Admin\Printful\productFormatt\Product;

require 'vendor/autoload.php';

class PrintfulCatalog
{
    private $cache; // Cache instance

    /**
     * Constructor for PrintfulCatalog class.
     *
     * @param CacheInterface $cache The cache instance to be used for caching data.
     */
    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Fetches data from the Printful API.
     */
    private function fetchDataFromApi(int $id, string $size): array
    {
        return Api::fetchDataFromApi($id, $size);
    }

    /**
     * Formats the product data retrieved from the API.
     */
    private function formatData(array $productData, array $sizeTableData, int $id, string $size): array
    {
        return Product::formatData($productData, $sizeTableData, $id, $size);
    }

    /**
     * Caches the formatted product data.
     
     */
    public function cacheData(array $formattedData, int $id, string $size): void
    {
        // Cache the data with a key based on product ID and size, expires in 5 minutes (300 seconds)
        $this->cache->set("product_{$id}_size_{$size}", $formattedData, 300);
    }

    /**
     * Retrieves the product and size information.
     * If the data is already cached, it returns the cached data. Otherwise, it fetches, formats, and caches the data.
     *
     */
    public function getProductAndSize(int $id, string $size): array
    {
        $cacheKey = "product_{$id}_size_{$size}";
        // Check if data is cached
        if ($cachedData = $this->cache->get($cacheKey)) {
            return $cachedData; // Return cached data if available
        }

        // If data is not cached, fetch from API, format, and cache
        [$productData, $sizeTableData] = $this->fetchDataFromApi($id, $size);
        $formattedData = $this->formatData($productData, $sizeTableData, $id, $size);
        $this->cacheData($formattedData, $id, $size);

        return $formattedData;
    }
}
