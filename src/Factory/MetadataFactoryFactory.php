<?php

namespace Vox\Metadata\Factory;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Metadata\Driver\DriverInterface;
use Metadata\MetadataFactory;
use Vox\Metadata\ClassMetadata;
use Vox\Metadata\Driver\AnnotationDriver;
use Vox\Metadata\Driver\YmlDriver;
use Vox\Metadata\MethodMetadata;
use Vox\Metadata\PropertyMetadata;

class MetadataFactoryFactory implements MetadataFactoryFactoryInterface
{
    public function createAnnotationMetadataFactory(
        string $metadataClassName = ClassMetadata::class,
        Reader $reader = null,
        string $methodMetadataClassName = MethodMetadata::class,
        string $propertyMetadataClassName = PropertyMetadata::class
    ) {
        return new MetadataFactory($this->createAnnotationMetadataDriver(
            $metadataClassName,
            $reader,
            $methodMetadataClassName,
            $propertyMetadataClassName
        ));
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
        string $metadataClassName,
        string $methodMetadataClassName = MethodMetadata::class,
        string $propertyMetadataClassName = PropertyMetadata::class,
        string $yamlExtension = 'yaml'
    ) {
        return new MetadataFactory($this->createYmlMetadataDriver(
            $metadataPath,
            $metadataClassName,
            $methodMetadataClassName,
            $propertyMetadataClassName,
            $yamlExtension
        ));
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
