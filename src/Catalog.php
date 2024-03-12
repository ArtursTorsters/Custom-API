<?php


require 'vendor/autoload.php';

use GuzzleHttp\Client;

class PrintfulCatalog
{
    public function getProduct(int $productId)
    {
        // Create a Guzzle HTTP client
        $client = new Client();

        // Construct the API endpoint URL for the product information
        $productApi = "https://api.printful.com/catalog/products/{$productId}";

        // Make a GET request to the Printful Catalog API to fetch product information
        $response = $client->get($productApi);

        // Check if the API request was successful (status code in the range of 200 to 299)
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            // Extract the JSON response body and decode it into an associative array
            $productData = json_decode($response->getBody(), true);

            // Check if the product data is valid
            if (isset($productData['result'])) {
                // Return the basic product information
                return [
                    'id' => $productData['result']['id'],
                    'title' => $productData['result']['title'],
                    'description' => $productData['result']['description'],
                ];
            } else {
                // Handle the case where the product data is not in the expected format
                return null;
            }
        } else {
            // Handle the case where the API request was not successful
            return null;
        }
    }
}

// Example usage
$printfulCatalog = new PrintfulCatalog();
$productInformation = $printfulCatalog->getProduct(438);

// Output or further processing
var_dump($productInformation);
