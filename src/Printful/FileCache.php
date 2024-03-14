<?php
namespace Admin\Printful;


class FileCache implements CacheInterface
{


    /**
     * Retrieve stored item.
     * Returns the same type as it was stored in.
     * Returns null if entry has expired.
     *
     * @param string $key
     * @return mixed|null
     */


    private $cacheData = [];
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
            }
        }

        return null;
    }
    /**
     * Store a mixed type value in cache for a certain amount of seconds.
     * Supported values should be scalar types and arrays.
     *
     * @param string $key
     * @param mixed $value
     * @param int $duration Duration in seconds
     * @return mixed
     */
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
