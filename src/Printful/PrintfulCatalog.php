<?php
namespace Admin\Printful;
require 'vendor/autoload.php';

use GuzzleHttp\Client;

class PrintfulCatalog
{
    public function getProductAndSize(int $productId)
    {
        $client = new Client();
        $productApi = "https://api.printful.com/products";

        $response = $client->get($productApi);

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $productData = json_decode($response->getBody(), true);

            if (isset($productData['result'])) {
                return [
                    'id' => $productData['result']['id'],
                    'title' => $productData['result']['title'],
                    'description' => $productData['result']['description'],
                ];
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
}

