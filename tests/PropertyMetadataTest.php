<?php

namespace Vox\Metadata\Test;

use PHPUnit\Framework\TestCase;
use Vox\Metadata\PropertyMetadata;

class PropertyMetadataTest extends TestCase
{
    public function testShouldParseDateTime() {
        $metadata = new PropertyMetadata(MyEvent::class, 'createdAt');

        $this->assertEquals(\DateTime::class, $metadata->typeInfo['class']);
        $this->assertEquals('Y-m-d H:i:s', $metadata->typeInfo['decoration']);
        $this->assertTrue($metadata->isDecoratedType());
        $this->assertTrue($metadata->isDateType());
    }

    public function testShouldParseDecorationColections() {
        $metadata1 = new PropertyMetadata(MyEvent::class, 'dates');

        $this->assertEquals('array', $metadata1->typeInfo['class']);
        $this->assertEquals(\DateTime::class, $metadata1->typeInfo['decoration']);

        $metadata2 = new PropertyMetadata(MyEvent::class, 'dates2');

        $this->assertEquals('array', $metadata2->typeInfo['class']);
        $this->assertEquals(\DateTime::class, $metadata2->typeInfo['decoration']);

        $metadata3 = new PropertyMetadata(MyEvent::class, 'relations');

        $this->assertEquals('array', $metadata3->typeInfo['class']);
        $this->assertEquals(MyEvent::class, $metadata3->typeInfo['decoration']);

        $metadata4 = new PropertyMetadata(MyEvent::class, 'relations2');

        $this->assertEquals(\Iterator::class, $metadata4->typeInfo['class']);
        $this->assertEquals(MyEvent::class, $metadata4->typeInfo['decoration']);
    }
}

class MyEvent {
    /**
     * @var \DateTime<Y-m-d H:i:s>
     */
    private \DateTime $createdAt;

    /**
     * @var array<\DateTime>
     */
    private array $dates;

    /**
     * @var \DateTime[]
     */
    private array $dates2;

    /**
     * @var array<MyEvent>
     */
    private $relations;

    /**
     * @var \Iterator<MyEvent>
     */
    private $relations2;

    public function __construct(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}