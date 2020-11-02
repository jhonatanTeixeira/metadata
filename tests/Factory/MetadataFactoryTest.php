<?php


namespace Vox\Metadata\Test;

use PHPUnit\Framework\TestCase;
use Vox\Metadata\ClassMetadata;
use Vox\Metadata\Factory\MetadataFactoryFactory;
use Vox\Metadata\Test\Stub\TestAnnotation;

class MetadataFactoryTest extends TestCase
{
    public function testShouldReadMetadata() {
        $factory = (new MetadataFactoryFactory())->createAnnotationMetadataFactory();

        $metadata = $factory->getMetadataForClass(MetadataStub::class);

        $this->doAssertions($metadata);
    }

    public function testShouldSerializeMetadata() {
        $factory = (new MetadataFactoryFactory())->createAnnotationMetadataFactory();

        $metadata = $factory->getMetadataForClass(MetadataStub::class);

        $serialized = serialize($metadata);

        $this->doAssertions(unserialize($serialized));
    }

    public function doAssertions(ClassMetadata $metadata) {
        $this->assertInstanceOf(ClassMetadata::class, $metadata);
        $this->assertTrue($metadata->hasAnnotation(TestAnnotation::class));
        $this->assertTrue($metadata->methodMetadata['getId']->hasAnnotation(TestAnnotation::class));
        $this->assertEquals('int', $metadata->methodMetadata['getId']->getReturnType());
        $this->assertEquals('int', $metadata->propertyMetadata['id']->type);
        $this->assertEquals('string', $metadata->propertyMetadata['name']->type);
        $this->assertFalse($metadata->propertyMetadata['id']->hasAnnotation(TestAnnotation::class));
        $this->assertTrue($metadata->propertyMetadata['name']->hasAnnotation(TestAnnotation::class));

        $this->assertEquals(MetadataStub::class, $metadata->propertyMetadata['parent']->type);
        $this->assertEquals(MetadataStub::class, $metadata->propertyMetadata['child']->type);
        $this->assertEquals('string', $metadata->propertyMetadata['someValue']->type);
        $this->assertEquals(MetadataStub::class, $metadata->propertyMetadata['someOtherValue']->type);

        $this->assertTrue($metadata->propertyMetadata['createdAt']->isDateType());
        $this->assertTrue($metadata->propertyMetadata['createdAt']->isNativeType());
        $this->assertTrue($metadata->propertyMetadata['many']->isDecoratedType());
        $this->assertEquals('array', $metadata->propertyMetadata['many']->typeInfo['class']);
        $this->assertEquals(MetadataStub::class, $metadata->propertyMetadata['many']->typeInfo['decoration']);
    }
}

/**
 * @TestAnnotation
 */
class MetadataStub {
    /**
     * @var int
     */
    public $id;

    /**
     * @TestAnnotation
     */
    public string $name;

    public MetadataStub $parent;

    /**
     * @var MetadataStub
     */
    public $child;

    public $someValue;

    public $someOtherValue;

    public \DateTime $createdAt;

    /**
     * @var array<MetadataStub>
     */
    public $many = [];

    /**
     * @TestAnnotation
     */
    public function getId(): int {
        return $this->id;
    }

    public function setSomeValue(string $someValue): void
    {
        $this->someValue = $someValue;
    }

    public function setSomeOtherValue(MetadataStub $someOtherValue): void
    {
        $this->someOtherValue = $someOtherValue;
    }
}