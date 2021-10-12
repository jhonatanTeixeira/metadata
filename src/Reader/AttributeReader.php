<?php

namespace Vox\Metadata\Reader;

use Doctrine\Common\Annotations\IndexedReader;
use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Reader to add the PHP 8+ attribute capability to annotation driver
 * 
 * @author Jhonatan Teixeira <jhonatan.teixeira@gmail.com>
 */
class AttributeReader implements Reader
{
    private IndexedReader $reader;
    
    public function __construct(IndexedReader $reader) 
    {
        $this->reader = $reader;
    }
    
    public function getClassAnnotation(ReflectionClass $class, $annotationName) 
    {
        $annotation = $this->reader->getClassAnnotation($class, $annotationName);
        $attribute = $class->getAttributes($annotationName)[0] ?? null;
        
        return $attribute ?: $annotation;
    }
    
    public function getMethodAnnotation(ReflectionMethod $method, $annotationName) 
    {
        $annotation = $this->reader->getMethodAnnotation($method, $annotationName);
        $attribute = $method->getAttributes($annotationName)[0] ?? null;
        
        return $attribute ? $attribute->newInstance() : $annotation;
    }
    
    public function getClassAnnotations(ReflectionClass $class) 
    {
        $annotations = $this->reader->getClassAnnotations($class);
        $attributes = [];
        
        foreach($class->getAttributes() as $attribute) {
            $attributes[$attribute->getName()] = $attribute->newInstance();
        }
        
        return array_merge($annotations, $attributes);
    }
    
    public function getMethodAnnotations(ReflectionMethod $method) 
    {
        $annotations = $this->reader->getMethodAnnotations($method);
        $attributes = [];
        
        foreach($method->getAttributes() as $attribute) {
            $attributes[$attribute->getName()] = $attribute->newInstance();
        }
        
        return array_merge($annotations, $attributes);
    }
    
    public function getPropertyAnnotation(ReflectionProperty $property, $annotationName) 
    {
        $annotation = $this->reader->getPropertyAnnotation($property, $annotationName);
        $attribute = $property->getAttributes($annotationName)[0] ?? null;
        
        return $attribute ? $attribute->newInstance() : $annotation;
    }
    
    public function getPropertyAnnotations(ReflectionProperty $property) 
    {
        $annotations = $this->reader->getPropertyAnnotations($property);
        $attributes = [];
        
        foreach($property->getAttributes() as $attribute) {
            $attributes[$attribute->getName()] = $attribute->newInstance();
        }
        
        return array_merge($annotations, $attributes);
    }
}
