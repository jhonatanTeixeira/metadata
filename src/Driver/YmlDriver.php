<?php

namespace Vox\Metadata\Driver;

use Metadata\Driver\DriverInterface;
use ProxyManager\Proxy\AccessInterceptorValueHolderInterface;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\Yaml\Parser;
use Vox\Metadata\ClassMetadata;
use Vox\Metadata\MethodMetadata;
use Vox\Metadata\PropertyMetadata;

/**
 * Yml driver to create a class metadata information
 * 
 * @author Jhonatan Teixeira <jhonatan.teixeira@gmail.com>
 */
class YmlDriver implements DriverInterface
{
    use TypeFromSetterTrait;

    private $ymlParser;
    
    private $path;
    
    private $classMetadataClassName;
    
    private $propertyMetadataClassName;

    private $methodMetadataClassName;

    private $yamlExtension;
    
    public function __construct(
        string $path,
        string $classMetadataClassName = ClassMetadata::class,
        string $propertyMetadataClassName = PropertyMetadata::class,
        string $methodMetadataClassName = MethodMetadata::class,
        string $yamlExtension = 'yaml'
    ) {
        $this->ymlParser                 = new Parser();
        $this->path                      = realpath($path);
        $this->classMetadataClassName    = $classMetadataClassName;
        $this->propertyMetadataClassName = $propertyMetadataClassName;
        $this->methodMetadataClassName   = $methodMetadataClassName;
        $this->yamlExtension             = $yamlExtension;
    }
    
    public function loadMetadataForClass(ReflectionClass $class): ClassMetadata
    {
        if ($class->implementsInterface(AccessInterceptorValueHolderInterface::class)) {
            $class = $class->getParentClass();
        }
        
        $yaml = $this->loadYml($class);

        /* @var $classMetadata ClassMetadata */
        $classMetadata = (new ReflectionClass($this->classMetadataClassName))->newInstance($class->name);
        $classMetadata->setAnnotations($this->getAnnotations($yaml, 'class'));

        foreach ($class->getMethods() as $method) {
            $methodMetadata = (new ReflectionClass($this->methodMetadataClassName))
                ->newInstance($class->name, $method->name);

            $methodMetadata->setAnnotations($this->getAnnotations($yaml, 'methods', $method->name));

            $classMetadata->addMethodMetadata($methodMetadata);
        }
        
        /* @var $property ReflectionProperty */
        foreach ($class->getProperties() as $property) {
            $propertyMetadata = (new ReflectionClass($this->propertyMetadataClassName))
                ->newInstance($class->name, $property->name);

            $propertyMetadata->setAnnotations($this->getAnnotations($yaml, 'properties', $property->name));

            $this->parseAccessors($propertyMetadata, $classMetadata);

            $classMetadata->addPropertyMetadata($propertyMetadata);
        }

        return $classMetadata;
    }
    
    private function loadYml(ReflectionClass $class)
    {
        $className = $class->getName();
        
        $path = sprintf(
            '%s/%s.%s',
            preg_replace('/\/$/', '', $this->path), 
            str_replace('\\', '.', $className),
            $this->yamlExtension
        );

        if (is_file($path)) {
            return $this->ymlParser->parse(file_get_contents($path));
        }
        
        $path = sprintf(
            '%s/%s.%s',
            preg_replace('/\/$/', '', $this->path), 
            str_replace('\\', '.', $className),
            $this->yamlExtension
        );
        
        if (is_file($path)) {
            return $this->ymlParser->parse(file_get_contents($path));
        }

        return [];
    }

    /**
     * @return object[]
     */
    private function getAnnotations(array $yaml, string $key, string $name = null): array {
        $annotations = [];

        $data = $name ? $yaml[$key][$name] ?? [] : $yaml[$key] ?? [];

        foreach ($data as $key => $value) {
            if (is_numeric($key)) {
                $annotationClass = $value;
                $params = [];
            } else {
                $annotationClass = $key;
                $params = $value;
            }

            $annotation = new $annotationClass();

            foreach ($params as $key => $value) {
                $annotation->{$key} = $value;
            }

            $annotations[$annotationClass] = $annotation;
        }

        return $annotations;
    }
}
