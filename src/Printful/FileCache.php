<?php
namespace Admin\Printful;

class FileCache implements CacheInterface
{
    private $cacheDirectory;

    /**
     * Constructor to set the cache directory.
     */
    public function __construct(string $cacheDirectory)
    {
        $this->cacheDirectory = $cacheDirectory;
    }

    /**
     * Retrieve a value from the cache.
     */
    public function get(string $key)
    {
        $filename = $this->getCacheFilename($key);
        if (file_exists($filename) && is_readable($filename)) {
            $data = unserialize(file_get_contents($filename));
            if ($data['expires_at'] > time()) {
                return $data['value'];
            }
        }
        return null;
    }

    /**
     * Store a value in the cache.
     */
    public function set(string $key, $value, int $duration)
    {
        $filename = $this->getCacheFilename($key);
        $data = [
            'value' => $value,
            'expires_at' => time() + $duration
        ];
        file_put_contents($filename, serialize($data));
    }

    /**
     * Get the cache filename for a given key.
     */
    private function getCacheFilename(string $key)
    {
        return $this->cacheDirectory . '/cache/' . md5($key);
    }
}
