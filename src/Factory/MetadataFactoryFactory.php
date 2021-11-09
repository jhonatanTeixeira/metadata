<?php

namespace Vox\Metadata\Factory;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Metadata\ClassHierarchyMetadata;
use Metadata\Driver\DriverInterface;
use Metadata\MetadataFactory;
use Vox\Metadata\ClassMetadata;
use Vox\Metadata\Driver\AnnotationDriver;
use Vox\Metadata\Driver\YmlDriver;
use Vox\Metadata\MethodMetadata;
use Vox\Metadata\PropertyMetadata;

class MetadataFactoryFactory implements MetadataFactoryFactoryInterface
{
    private bool $debug;

    /**
     * @param bool $debug whether to enable debug or not
     */
    public function __construct(bool $debug = false)
    {
        $this->debug = $debug;
    }

    public function createAnnotationMetadataFactory(
        string $metadataClassName = ClassMetadata::class,
        Reader $reader = null,
        string $methodMetadataClassName = MethodMetadata::class,
        string $propertyMetadataClassName = PropertyMetadata::class
    ) {
        return new MetadataFactory(
            $this->createAnnotationMetadataDriver(
                $metadataClassName,
                $reader,
                $methodMetadataClassName,
                $propertyMetadataClassName
            ),
            ClassHierarchyMetadata::class,
            $this->debug
        );
    }
    
    private function createAnnotationMetadataDriver(
        string $metadataClassName = ClassMetadata::class,
        Reader $reader = null,
        string $methodMetadataClassName = MethodMetadata::class,
        string $propertyMetadataClassName = PropertyMetadata::class
    ): DriverInterface {
        return new AnnotationDriver(
            $reader ?? new AnnotationReader(),
            $metadataClassName,
            $propertyMetadataClassName,
            $methodMetadataClassName
        );
    }

    public function createYmlMetadataFactory(
        string $metadataPath,
        string $metadataClassName = ClassMetadata::class,
        string $methodMetadataClassName = MethodMetadata::class,
        string $propertyMetadataClassName = PropertyMetadata::class,
        string $yamlExtension = 'yaml'
    ) {
        return new MetadataFactory(
            $this->createYmlMetadataDriver(
                $metadataPath,
                $metadataClassName,
                $methodMetadataClassName,
                $propertyMetadataClassName,
                $yamlExtension
            ),
            ClassHierarchyMetadata::class,
            $this->debug
        );
    }

    private function createYmlMetadataDriver(
        string $metadataPath,
        string $metadataClassName = ClassMetadata::class,
        string $methodMetadataClassName = MethodMetadata::class,
        string $propertyMetadataClassName = PropertyMetadata::class,
        string $yamlExtension = 'yaml'
    ): DriverInterface {
        return new YmlDriver(
            $metadataPath,
            $metadataClassName,
            $propertyMetadataClassName,
            $methodMetadataClassName,
            $yamlExtension
        );
    }
}
