<?php

namespace Vox\Metadata;

class ParamMetadata implements \Serializable
{
    public $method;

    public $name;

    public $reflection;

    public $type;

    public function __construct(\Metadata\MethodMetadata $method, string $name, \ReflectionParameter $reflection = null)
    {
        $this->method     = $method;
        $this->name       = $name;
        $this->reflection = $reflection;

        if (!$reflection) {
            $this->loadReflection();
        }

        $class = $this->reflection->getClass() ? $this->reflection->getClass()->name : null;
        $type  = $this->reflection->hasType() ? $this->reflection->getType()->getName() : null;

        $this->type = $class ?? $type;
    }

    private function loadReflection() {
        $this->reflection = new \ReflectionParameter([$this->method->class, $this->method->name], $this->name);
    }

    public function serialize()
    {
        return serialize([$this->method, $this->name, $this->type]);
    }

    public function unserialize($serialized)
    {
        [$this->method, $this->name, $this->type] = $this->unserialize($serialized);
        $this->loadReflection();
    }
}