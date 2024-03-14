<?php


namespace Admin\Printful\api;

use GuzzleHttp\Client;

class Api
{
    public static function fetchDataFromApi(int $id)
    {
        $client = new Client();
        $productApi = "https://api.printful.com/products/{$id}";
        $sizeTableApi = "https://api.printful.com/products/{$id}/sizes";

        $productResponse = $client->get($productApi);
        $sizeTableResponse = $client->get($sizeTableApi);

        $productData = json_decode($productResponse->getBody(), true);
        $sizeTableData = json_decode($sizeTableResponse->getBody(), true);

        return [$productData, $sizeTableData];
    }
}
