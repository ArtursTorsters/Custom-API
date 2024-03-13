<?php
require 'vendor/autoload.php';

use Admin\Printful\FileCache;
use Admin\Printful\PrintfulCatalog;

// the file cache to $cache
$cache = new FileCache(__DIR__ . '/src/Printful');

// catalog with cache
$printfulCatalog = new PrintfulCatalog($cache);

// request L & 438 id
$id = 438;
$size = 'L';

$result = $printfulCatalog->getProductAndSize($id, $size);

if ($result !== null) {
    echo "Product ID: {$result['product']['id']}\n";
    echo "Product Title: {$result['product']['title']}\n";
    echo "Product Description: {$result['product']['description']}\n";

    // Output size table information
    echo "\nSize Table:\n";
    echo "Type: {$result['size_table']['type']}\n";
    echo "Unit: {$result['size_table']['unit']}\n";
    echo "Description: {$result['size_table']['description']}\n";

    // Output measurements
    echo "\nMeasurements:\n";
    foreach ($result['size_table']['measurements'] as $measurement) {
        echo "Type Label: {$measurement['type_label']}, Value: {$measurement['value']}\n";
    }
} 