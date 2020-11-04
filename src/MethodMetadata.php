<?php

namespace Vox\Metadata;

use Metadata\MethodMetadata as BaseMetadata;

class MethodMetadata extends BaseMetadata
{
    use AnnotationsTrait, FunctionResolverTrait;

    public $returnType;

    public $params;

    public function __construct($class, $name)
    {
        parent::__construct($class, $name);

        $this->resolveReturnType();
        $this->resolveParams();
    }

    public function serialize()
    {
        return serialize([
            $this->class,
            $this->name,
            $this->annotations,
            $this->returnType,
            $this->params,
        ]);
    }

    public function unserialize($str)
    {
        [$this->class, $this->name, $this->annotations, $this->returnType, $this->params] = unserialize($str);

        $this->reflection = new \ReflectionMethod($this->class, $this->name);
        $this->reflection->setAccessible(true);
    }

    public function getReturnType() {
        return $this->returnType;
    }
}
