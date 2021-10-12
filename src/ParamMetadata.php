<?php

namespace Vox\Metadata;

use ReflectionParameter;

class ParamMetadata implements \Serializable
{
    public $class;

    public $method;

    public $name;

    public $reflection;

    public $type;

    public function __construct($class, $method, $name)
    {
        $this->class      = $class;
        $this->method     = $method;
        $this->name       = $name;

        $this->loadReflection();

        $type = $this->reflection->hasType() ? $this->reflection->getType()->getName() : null;

        $this->type = $type;
    }

    private function loadReflection() 
    {
        $callable = $this->class ? [$this->class, $this->method] : $this->method;
        $this->reflection = new ReflectionParameter($callable, $this->name);
    }

    public function serialize()
    {
        return serialize([$this->class, $this->method, $this->name, $this->type]);
    }

    public function unserialize($serialized)
    {
        [$this->class, $this->method, $this->name, $this->type] = unserialize($serialized);
        $this->loadReflection();
    }
}