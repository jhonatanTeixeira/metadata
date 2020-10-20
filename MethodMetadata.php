<?php

namespace Vox\Metadata;

use Metadata\MethodMetadata as BaseMetadata;

class MethodMetadata extends BaseMetadata
{
    use AnnotationsTrait;
    
    public function serialize()
    {
        return serialize([
            $this->class,
            $this->name,
            $this->annotations
        ]);
    }

    public function unserialize($str)
    {
        [$this->class, $this->name, $this->annotations] = unserialize($str);

        $this->reflection = new \ReflectionMethod($this->class, $this->name);
        $this->reflection->setAccessible(true);
    }

}
