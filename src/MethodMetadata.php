<?php

namespace Vox\Metadata;

use Metadata\MethodMetadata as BaseMetadata;

class MethodMetadata extends BaseMetadata
{
    use AnnotationsTrait;

    public $returnType;

    public $params;

    public function __construct($class, $name)
    {
        parent::__construct($class, $name);

        $this->returnType = $this->reflection->hasReturnType()
            ? $this->reflection->getReturnType()->getName()
            : null;

        $this->params = array_map(
            fn($param) => new ParamMetadata($class, $name, $param->name),
            $this->reflection->getParameters()
        );
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
