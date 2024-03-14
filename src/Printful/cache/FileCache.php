<?php
namespace Admin\Printful\cache;
class FileCache implements CacheInterface
{
    private $cacheDirectory;

//   cache directory
    public function __construct(string $cacheDirectory)
    {
        $cacheDirectory = 'cache';
        $cache = new FileCache($cacheDirectory);
        
        
    }

//    getting value from cache
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

//   store value in cache
// store value in cache
public function set(string $key, $value, int $duration)
{
    $filename = $this->getCacheFilename($key);
    echo "Filename: $filename\n"; // Print the filename for debugging
    $data = [
        'value' => $value,
        'expires_at' => time() + $duration
    ];
    file_put_contents($filename, serialize($data));
}


//    generate file on key
private function getCacheFilename(string $key)
{
    
    return $this->cacheDirectory . '' . md5($key);
    
}

}
