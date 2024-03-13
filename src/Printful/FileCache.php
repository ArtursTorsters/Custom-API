<?php
namespace Admin\Printful;

class FileCache implements CacheInterface
{
    // Array to hold cached data
    private $cacheData = [];

    // Retrieve a value from the cache
    public function get(string $key)
    {
        // Check if the key exists in the cache
        if (isset($this->cacheData[$key])) {
            // Get the cached item
            $cachedItem = $this->cacheData[$key];

            // Check if the item has not expired
            if ($cachedItem['expires_at'] > time()) {
                // Return the cached value
                return $cachedItem['value'];
            } else {
                // Remove the expired item from the cache
                unset($this->cacheData[$key]);
            }
        }

        return null;
    }

    // Store a value in the cache
    public function set(string $key, $value, int $duration)
    {
        // Calculate the expiration time based on the duration
        $expiresAt = time() + $duration;

        // Store the value along with the expiration time in the cache
        $this->cacheData[$key] = [
            'value' => $value,
            'expires_at' => $expiresAt,
        ];
    }
}
