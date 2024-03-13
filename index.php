<?php
require 'vendor/autoload.php';

use Admin\Printful\FileCache;
use Admin\Printful\PrintfulCatalog;

// the filecache to $cache
$cache = new FileCache(__DIR__ . '/src/Printful');

// catalago with cache
$printfulCatalog = new PrintfulCatalog($cache);

// request L & 438 id
$id = 438;
$size = 'L';


$result = $printfulCatalog->getProductAndSize($id, $size);


if ($result !== null) {
    echo "Product ID: {$result['id']}\n";
    echo "Product Title: {$result['title']}\n";
    echo "Product Description: {$result['description']}\n";
  

} else {
    echo "Unable to retrieve product and size information.\n";
}
