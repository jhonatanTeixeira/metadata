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

    public function loadClassMetadataFromCache(\ReflectionClass $class)
    {
        return $this->cache->get("{$this->prefix}.{$class->name}");
    }

    public function putClassMetadataInCache(ClassMetadata $metadata)
    {
        return $this->cache->set("{$this->prefix}.{$metadata->name}", $metadata);
    }

    public function evictClassMetadataFromCache(\ReflectionClass $class)
    {
        $key = "{$this->prefix}.{$class->name}";

        if ($this->cache->has($key)) {
            return $this->cache->delete($key);
        }
    }
}