<?php


namespace Vox\Metadata\Test;

use PHPUnit\Framework\TestCase;
use Vox\Metadata\ClassMetadata;
use Vox\Metadata\Factory\MetadataFactoryFactory;
use Vox\Metadata\MethodMetadata;
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

    public function testShouldReadYamlMetadata() {
        $factory = (new MetadataFactoryFactory())->createYmlMetadataFactory(__DIR__ . '/../fixtures');

        $metadata = $factory->getMetadataForClass(MetadataStubYaml::class);

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
        $this->assertEquals(MetadataStub::class, $metadata->methodMetadata['setSomeOtherValue']->params[0]->type);

        $this->assertCount(1, $metadata->getAnnotations());
        $this->assertInstanceOf(TestAnnotation::class, $metadata->getAnnotation(TestAnnotation::class));
        $this->assertEquals('int', $metadata->propertyMetadata['extra']->type);
        $this->assertTrue($metadata->propertyMetadata['overriden']->hasAnnotation(TestAnnotation::class));

        $this->assertTrue($metadata->propertyMetadata['someValue']->hasSetter());
        $this->assertTrue($metadata->propertyMetadata['id']->hasGetter());
        $this->assertInstanceOf(MethodMetadata::class, $metadata->propertyMetadata['someValue']->setter);
        $this->assertEquals('getId', $metadata->propertyMetadata['id']->getter->name);
        $this->assertEquals('setSomeValue', $metadata->propertyMetadata['someValue']->setter->name);
    }
}

class ParentStub {
    public int $extra;

    public $overriden;
}

/**
 * @TestAnnotation
 */
class MetadataStub extends ParentStub {
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
    public $overriden;

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

class ParentStubYaml {
    public int $extra;

    public $overriden;
}

class MetadataStubYaml extends ParentStubYaml {
    /**
     * @var int
     */
    public $id;

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

    public $overriden;

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
