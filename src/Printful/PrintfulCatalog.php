<?php

namespace Admin\Printful;

use Admin\Printful\api\Api;
use Admin\Printful\productFormatt\Product;

require 'vendor/autoload.php';

/**
 * PrintfulCatalog class manages the retrieval, formatting, and caching of product data from the Printful API.
 */
class PrintfulCatalog
{
    /** @var CacheInterface The cache instance used for caching data */
    private $cache;

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
     *
     * @param int $id The ID of the product.
     * @param string $size The size of the product.
     * @return array The fetched data from the API.
     */
    private function fetchDataFromApi(int $id, string $size)
    {
        return Api::fetchDataFromApi($id, $size);
    }

    /**
     * Formats the product data retrieved from the API.
     *
     * @param array $productData The raw product data from the API.
     * @param array $sizeTableData The raw size table data from the API.
     * @param int $id The ID of the product.
     * @param string $size The size of the product.
     * @return array The formatted product data.
     */
    private function formatData(array $productData, array $sizeTableData, int $id, string $size)
    {
        return Product::formatData($productData, $sizeTableData, $id, $size);
    }

    /**
     * Caches the formatted product data.
     *
     * @param array $formattedData The formatted product data to be cached.
     * @param int $id The ID of the product.
     * @param string $size The size of the product.
     * @return void
     */
    public function cacheData(array $formattedData, int $id, string $size)
    {
        $cacheKey = "product_{$id}_size_{$size}";
        $this->cache->set($cacheKey, $formattedData, 300);
    }

    /**
     * Retrieves the product and size information.
     * If the data is already cached, it returns the cached data. Otherwise, it fetches, formats, and caches the data.
     *
     * @param int $id The ID of the product.
     * @param string $size The size of the product.
     * @return array The product and size information.
     */
    public function getProductAndSize(int $id, string $size)
    {
        $cacheKey = "product_{$id}_size_{$size}";
        $cachedData = $this->cache->get($cacheKey);

        if ($cachedData !== null) {
            return $cachedData;
        }

        $apiData = $this->fetchDataFromApi($id, $size);
        $formattedData = $this->formatData($apiData[0], $apiData[1], $id, $size);

        $this->cacheData($formattedData, $id, $size);

        return $formattedData;
    }
}
