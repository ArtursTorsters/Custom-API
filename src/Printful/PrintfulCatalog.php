<?php

namespace Admin\Printful;

use Admin\Printful\api\Api;
use Admin\Printful\productFormatt\Product;

require 'vendor/autoload.php';

class PrintfulCatalog
{
    private $cache; // Cache instance

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    //api
    private function fetchDataFromApi(int $id, string $size): array
    {
        return Api::fetchDataFromApi($id, $size);
    }

    //format api
    private function formatData(array $productData, array $sizeTableData, int $id, string $size): array
    {
        return Product::formatData($productData, $sizeTableData, $id, $size);
    }

    //cache data
    public function cacheData(array $formattedData, int $id, string $size): void
    {
        //cache data
        $this->cache->set("product_{$id}_size_{$size}", $formattedData, 300);
    }


    public function getProductAndSize(int $id, string $size): array
    {
        $cacheKey = "product_{$id}_size_{$size}";
        //Check if data is cached
        if ($cachedData = $this->cache->get($cacheKey)) {
            return $cachedData;
        }

        //if not cached
        [$productData, $sizeTableData] = $this->fetchDataFromApi($id, $size);
        $formattedData = $this->formatData($productData, $sizeTableData, $id, $size);
        $this->cacheData($formattedData, $id, $size);

        return $formattedData;
    }
}
