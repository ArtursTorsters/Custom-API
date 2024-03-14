<?php

namespace Admin\Printful\productFormatt;

use Admin\Printful\PrintfulCatalog;

class Product
{
    private static $printfulCatalog;

    public static function setPrintfulCatalog(PrintfulCatalog $printfulCatalog)
    {
        self::$printfulCatalog = $printfulCatalog;
    }
    public static function formatData(array $productData, array $sizeTableData, int $id, string $size)
    {
        // Extract product and size information
        $product = self::extractProduct($productData, $id);

        // Extract size table information
        $sizeTable = self::extractSizeTable($sizeTableData, $size);

        // Check if product information is available
        if (isset($product['id'], $product['title'], $product['description'])) {
            // Combine product, size, and size table data
            $formattedData = ['product' => $product, 'size' => $size, 'size_table' => $sizeTable];

            // Cache the formatted data for 5 minutes using the cacheData method from PrintfulCatalog
            self::$printfulCatalog->cacheData($formattedData, $id, $size);

            return $formattedData;
        }
    }

    public static function extractProduct(array $productData, int $id)
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

    public static function extractSizeTable(array $sizeTableData, string $size)
    {
        $sizeTables = $sizeTableData['result']['size_tables'];
        foreach ($sizeTables as $sizeTable) {
            return [
                'type' => $sizeTable['type'],
                'unit' => $sizeTable['unit'],
                'description' => $sizeTable['description'],
                'measurements' => self::extractSizeTableMeasurements($sizeTable, $size),
            ];
        }
        return null;
    }

    // measurements from the size table data
    public static function extractSizeTableMeasurements(array $sizeTable, string $size)
    {
        $measurements = [];

        // Get the first measurement entry (assuming there's only one)
        $measurement = $sizeTable['measurements'][0];

        if ($measurement !== null && isset($measurement['values']) && is_array($measurement['values'])) {
            foreach ($measurement['values'] as $sizeData) {
                // Check if the size matches the requested size
                if ($sizeData['size'] === $size) {
                    $measurements[] = [
                        'type_label' => $measurement['type_label'] ?? null,
                        'value' => $sizeData['value'] ?? null,
                    ];
                }
            }
        }

        return $measurements;
    }
}
