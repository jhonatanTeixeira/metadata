<?php

namespace Vox\Metadata;

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

        $class = $this->reflection->getClass() ? $this->reflection->getClass()->name : null;
        $type  = $this->reflection->hasType() ? $this->reflection->getType()->getName() : null;

        $this->type = $class ?? $type;
    }

    private function loadReflection() {
        $this->reflection = new \ReflectionParameter([$this->class, $this->method], $this->name);
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