<?php

namespace Vox\Metadata\Factory;

use Doctrine\Common\Annotations\Reader;
use Metadata\MetadataFactory;
use Vox\Metadata\ClassMetadata;
use Vox\Metadata\MethodMetadata;
use Vox\Metadata\PropertyMetadata;

/**
 * @author Jhonatan Teixeira <jhonatan.teixeira@gmail.com>
 */
interface MetadataFactoryFactoryInterface
{
    /**
     * @param string $metadataClassName the fqcn to be used as class metadata holder, must implement the interface
     * @param Reader|null $reader the desired annotation reader, a custom one can be derived from the doctrine interface
     * @param string $methodMetadataClassName the fqcn to be used as method metadata holder, must implement the interface
     * @param string $propertyMetadataClassName the fqcn to be used as property metadata holder, must implement the interface
     *
     * @return MetadataFactory
     */
    public function createAnnotationMetadataFactory(
        string $metadataClassName = ClassMetadata::class,
        Reader $reader = null,
        string $methodMetadataClassName = MethodMetadata::class,
        string $propertyMetadataClassName = PropertyMetadata::class
    );

    /**
     * @param string $metadataPath the path for the folder containing the metadata yaml files
     * @param string $metadataClassName the fqcn to be used as class metadata holder, must implement the interface
     * @param string $methodMetadataClassName the fqcn to be used as method metadata holder, must implement the interface
     * @param string $propertyMetadataClassName the fqcn to be used as property metadata holder, must implement the interface
     * @param string $yamlExtension the desired extension for the yaml files
     *
     * @return MetadataFactory
     */
    public function createYmlMetadataFactory(
        string $metadataPath,
        string $metadataClassName = ClassMetadata::class,
        string $methodMetadataClassName = MethodMetadata::class,
        string $propertyMetadataClassName = PropertyMetadata::class,
        string $yamlExtension = 'yaml'
    );
}
