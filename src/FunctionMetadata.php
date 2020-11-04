<?php

namespace Vox\Metadata;

/**
 * Bridge for seamless behavior between functions and class methods
 */
class FunctionMetadata extends MethodMetadata
{
    public $function;

    public $reflection;

    public $class = null;

    public $name;

    /**
     * FunctionMetadata constructor.
     * @param $function
     */
    public function __construct(callable $function)
    {
        $this->function = $this->name = $function;

        $this->loadReflection();
        $this->resolveReturnType();
        $this->resolveParams();
    }

    protected function loadReflection() {
        $this->reflection = new \ReflectionFunction($this->function);
    }

    public function invoke($obj, array $args = []) {
        return call_user_func_array([$this, 'function'], $args);
    }

    public function __invoke(...$args)
    {
        return $this->invoke(null, $args);
    }
}