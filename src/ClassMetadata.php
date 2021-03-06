<?php

namespace Vox\Metadata;

use Metadata\MergeableClassMetadata;
use Metadata\MergeableInterface;

/**
 * Holds all metadata for a single class
 * 
 * @author Jhonatan Teixeira <jhonatan.teixeira@gmail.com>
 */
class ClassMetadata extends MergeableClassMetadata
{
    use AnnotationsTrait;
    
    public function serialize()
    {
        return serialize(array(
            $this->name,
            $this->methodMetadata,
            $this->propertyMetadata,
            $this->fileResources,
            $this->createdAt,
            $this->annotations,
        ));
    }

    public function unserialize($str)
    {
        list(
            $this->name,
            $this->methodMetadata,
            $this->propertyMetadata,
            $this->fileResources,
            $this->createdAt,
            $this->annotations
        ) = unserialize($str);

        $this->reflection = new \ReflectionClass($this->name);
    }

    public function merge(MergeableInterface $object)
    {
        parent::merge($object);
        $this->annotations = array_merge($this->annotations, $object->annotations);
    }
}
