<?php

require 'vendor/autoload.php';

use Admin\Printful\FileCache;
use Admin\Printful\PrintfulCatalog;

$cache = new FileCache(__DIR__ . '/src/Printful');
$printfulCatalog = new PrintfulCatalog($cache);

$result = $printfulCatalog->getProductAndSize(438, 'L');

// Output the result
var_dump($result);
