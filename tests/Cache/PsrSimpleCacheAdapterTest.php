<?php


namespace Vox\Metadata\Test\Cache;

use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;
use Vox\Metadata\Cache\PsrSimpleCacheAdapter;
use Vox\Metadata\ClassMetadata;
use Vox\Metadata\Factory\MetadataFactoryFactory;

class PsrSimpleCacheAdapterTest extends TestCase
{
    public function testShouldLoadMetadataFromCache() {
        $metadata = new ClassMetadata(SomeCacheStub::class);
        $metadata->fileResources[] = __FILE__;
        $metadata->createdAt = filemtime(__FILE__) - 1000;

        $simpleCacheMock = $this->createMock(CacheInterface::class);
        $simpleCacheMock
            ->expects($this->once())
            ->method('get')
            ->with('metadata.' . SomeCacheStub::class)
            ->willReturn($metadata);

        $simpleCacheMock
            ->expects($this->once())
            ->method('has')
            ->with('metadata.' . SomeCacheStub::class)
            ->willReturn(true);

        $simpleCacheMock
            ->expects($this->once())
            ->method('delete')
            ->with('metadata.' . SomeCacheStub::class)
            ->willReturn($metadata);

        $simpleCacheMock
            ->expects($this->once())
            ->method('set')
            ->with('metadata.' . SomeCacheStub::class, $this->anything());

        $metadataFactory = (new MetadataFactoryFactory(true))->createAnnotationMetadataFactory();
        $metadataFactory->setCache(new PsrSimpleCacheAdapter($simpleCacheMock));

        $metadata = $metadataFactory->getMetadataForClass(SomeCacheStub::class);

        $this->assertEquals(SomeCacheStub::class, $metadata->name);
    }
}

class SomeCacheStub
{
    private $param1;

    private $param2;
}