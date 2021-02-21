<?php


namespace Vox\Metadata\Cache;


use Metadata\Cache\CacheInterface;
use Metadata\ClassMetadata;
use Psr\SimpleCache\CacheInterface as PsrCacheInterface;

class PsrSimpleCacheAdapter implements CacheInterface
{
    private PsrCacheInterface $cache;

    private string $prefix;

    public function __construct(PsrCacheInterface $cache, string $prefix = 'metadata')
    {
        $this->cache = $cache;
        $this->prefix = $prefix;
    }

    private function getClassName($metadata)
    {
        return str_replace('\\', '.', $metadata->name);
    }

    public function loadClassMetadataFromCache(\ReflectionClass $class)
    {
        $className = $this->getClassName($class);
        return $this->cache->get("{$this->prefix}.{$className}");
    }

    public function putClassMetadataInCache(ClassMetadata $metadata)
    {
        $className = $this->getClassName($metadata);
        return $this->cache->set("{$this->prefix}.{$className}", $metadata);
    }

    public function evictClassMetadataFromCache(\ReflectionClass $class)
    {
        $className = $this->getClassName($class);
        $key = "{$this->prefix}.{$className}";

        if ($this->cache->has($key)) {
            return $this->cache->delete($key);
        }
    }
}