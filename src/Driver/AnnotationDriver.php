<?php

namespace Vox\Metadata\Driver;

use Doctrine\Common\Annotations\IndexedReader;
use Doctrine\Common\Annotations\Reader;
use Metadata\ClassMetadata as BaseClassMetadata;
use Metadata\Driver\DriverInterface;
use Vox\Metadata\MethodMetadata;
use ProxyManager\Proxy\AccessInterceptorValueHolderInterface;
use ReflectionClass;
use Vox\Metadata\ClassMetadata;
use Vox\Metadata\PropertyMetadata;

/**
 * Driver to create classes metadata using annotations, depends on doctrine's annotation reader
 * 
 * @author Jhonatan Teixeira <jhonatan.teixeira@gmail.com>
 */
class AnnotationDriver implements DriverInterface
{
    use TypeFromSetterTrait;
    
    /**
     * @var Reader
     */
    private $annotationReader;
    
    private $classMetadataClassName;
    
    private $propertyMetadataClassName;
    
    private $methodMetadataClassName;
    
    public function __construct(
        Reader $annotationReader,
        string $classMetadataClassName = ClassMetadata::class,
        string $propertyMetadataClassName = PropertyMetadata::class,
        string $methodMetadataClassName = MethodMetadata::class
    ) {
        $this->annotationReader          = new IndexedReader($annotationReader);
        $this->classMetadataClassName    = $classMetadataClassName;
        $this->propertyMetadataClassName = $propertyMetadataClassName;
        $this->methodMetadataClassName   = $methodMetadataClassName;
    }
    
    public function loadMetadataForClass(ReflectionClass $class): BaseClassMetadata
    {
        if ($class->implementsInterface(AccessInterceptorValueHolderInterface::class)) {
            $class = $class->getParentClass();
        }

        /* @var $classMetadata ClassMetadata */
        $classMetadata    = (new ReflectionClass($this->classMetadataClassName))->newInstance($class->name);
        $classAnnotations = $this->annotationReader->getClassAnnotations($class);

        $classMetadata->setAnnotations($classAnnotations);
        
        foreach ($class->getMethods() as $method) {
            $methodMatadata = (new ReflectionClass($this->methodMetadataClassName))
                ->newInstance($class->name, $method->name);
            $methodMatadata->setAnnotations($this->annotationReader->getMethodAnnotations($method));
            $classMetadata->addMethodMetadata($methodMatadata);
        }
        
        foreach ($class->getProperties() as $property) {
            $propertyAnnotations = $this->annotationReader->getPropertyAnnotations($property);
            $propertyMetadata = (new ReflectionClass($this->propertyMetadataClassName))
                ->newInstance($class->name, $property->name);
            $propertyMetadata->setAnnotations($propertyAnnotations);

            $this->parseAccessors($propertyMetadata, $classMetadata);

            $classMetadata->addPropertyMetadata($propertyMetadata);
        }
        
        return $classMetadata;
    }
}
