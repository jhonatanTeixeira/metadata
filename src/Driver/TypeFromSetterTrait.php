<?php

namespace Vox\Metadata\Driver;

use Exception;
use Metadata\ClassMetadata;
use Metadata\MethodMetadata;
use Metadata\PropertyMetadata;

/**
 * snipet to retreave a property type through it's setter type hint
 * 
 * @author Jhonatan Teixeira <jhonatan.teixeira@gmail.com>
 */
trait TypeFromSetterTrait
{
    private function getTypeFromSetter(PropertyMetadata $propertyMetadata, ClassMetadata $classMetadata)
    {
        $setter = $this->getSetter($propertyMetadata, $classMetadata);
        
        if ($setter instanceof MethodMetadata) {
            $params = $setter->reflection->getParameters();
            
            if (count($params) == 0) {
                throw new Exception("setter method {$classMetadata->name}:{$setterName} has no params");
            }
            
            if (count($params) > 1) {
                throw new Exception("setter method {$classMetadata->name}:{$setterName} has more than one param");
            }
            
            return $params[0]->hasType() ? $params[0]->getType()->getName() : null;
        }
    }

    private function getSetter(PropertyMetadata $propertyMetadata, ClassMetadata $classMetadata) {
        $setterName = sprintf('set%s', ucfirst($propertyMetadata->name));

        return $classMetadata->methodMetadata[$setterName] ?? null;
    }

    private function getGetter(PropertyMetadata $propertyMetadata, ClassMetadata $classMetadata) {
        $getterName = sprintf('get%s', ucfirst($propertyMetadata->name));

        return $classMetadata->methodMetadata[$getterName] ?? null;
    }

    private function parseAccessors(PropertyMetadata $propertyMetadata, ClassMetadata $classMetadata) {
        if (property_exists($propertyMetadata, 'type') && empty($propertyMetadata->type)) {
            $propertyMetadata->type = $this->getTypeFromSetter($propertyMetadata, $classMetadata);
        }

        if (property_exists($propertyMetadata, 'setter') && empty($propertyMetadata->setter)) {
            $propertyMetadata->setter = $this->getSetter($propertyMetadata, $classMetadata);
        }

        if (property_exists($propertyMetadata, 'getter') && empty($propertyMetadata->getter)) {
            $propertyMetadata->getter = $this->getGetter($propertyMetadata, $classMetadata);
        }
    }
}
