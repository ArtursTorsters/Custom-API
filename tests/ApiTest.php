<?php

namespace Admin\Printful\api;

use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    public function testApi()
    {
        $api = new Api();
        $id = 438;

        $response = $api->fetchDataFromApi($id);

        // response is arr
        $this->assertIsArray($response);
        $this->assertCount(2, $response);

        // Assert that each element of the response is an array
        $this->assertIsArray($response[0]); // prod
        $this->assertIsArray($response[1]); // size tab
    }
}
