<?php
namespace Admin\Printful;

class FileCache implements CacheInterface
{
    private $cacheDirectory;

//   cache directory
    public function __construct(string $cacheDirectory)
    {
        $this->cacheDirectory = $cacheDirectory;
    }

// getting value from cache
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

//store value in cache
    public function set(string $key, $value, int $duration)
    {
        $filename = $this->getCacheFilename($key);
        $data = [
            'value' => $value,
            'expires_at' => time() + $duration
        ];
        file_put_contents($filename, serialize($data));
    }

//cached fileName based on the key
    private function getCacheFilename(string $key)
    {
        return $this->cacheDirectory . '/cacheData/' . md5($key);
    }
}
