<?php

use PHPUnit\Framework\TestCase;
use Admin\Printful\FileCache;

// set and get method test

class FileCacheTest extends TestCase
{
    public function testGet(): void
    {
        $cache = new FileCache('/cache/');
        $result = $cache->get('test_key');

        $this->assertNull($result);
    }

    public function testSet(): void
    {
        $cache = new FileCache('/cache/');
        $cache->set('test_key', 'test_value', 60);

        $this->assertTrue(true);
    }
}


