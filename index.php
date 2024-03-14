<?php
require 'vendor/autoload.php';

use Admin\Printful\FileCache;
use Admin\Printful\PrintfulCatalog;
use Admin\Printful\productFormatt\Product;

$cache = new FileCache(__DIR__ . '/src/Printful');
$printfulCatalog = new PrintfulCatalog($cache);

Product::setPrintfulCatalog($printfulCatalog);


$id = 438;
$size = 'L';

// call getProductAndSize
$result = $printfulCatalog->getProductAndSize($id, $size);

if ($result !== null) {
    echo "Product ID: {$result['product']['id']}\n";
    echo "Product Title: {$result['product']['title']}\n";
    echo "Product Description: {$result['product']['description']}\n";
    echo "Type: {$result['size_table']['type']}\n";
    echo "Unit: {$result['size_table']['unit']}\n";
    $text = "Description: {$result['size_table']['description']}\n";
    echo strip_tags($text, '<script>');    echo "Measurements:\n";
    foreach ($result['size_table']['measurements'] as $measurement) {
        echo "Type Label: {$measurement['type_label']}\n";
        echo "Value: {$measurement['value']}\n";
    }
}