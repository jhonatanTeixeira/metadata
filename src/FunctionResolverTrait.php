<?php


namespace Vox\Metadata;


trait FunctionResolverTrait
{
    public $returnType;

    public $params;

    protected function resolveReturnType()
    {
        $this->returnType = $this->reflection->hasReturnType()
            ? $this->reflection->getReturnType()->getName()
            : null;
    }

    protected function resolveParams() {
        $this->params = array_map(
            fn($param) => new ParamMetadata($this->class, $this->name, $param->name),
            $this->reflection->getParameters()
        );
    }
}