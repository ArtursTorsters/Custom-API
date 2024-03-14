<?php

namespace Admin\Printful\api;

use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    public function testFetchDataFromApi()
    {
        $api = new Api();
        $id = 438;
        $response = $api->fetchDataFromApi($id);

    //  if the response is not empty
        $this->assertNotEmpty($response);
    }
}
