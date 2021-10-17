<?php

namespace Vox\Metadata;

use Metadata\PropertyMetadata as BaseMetadata;
use ProxyManager\Proxy\AccessInterceptorValueHolderInterface;
use ReflectionClass;

/**
 * Holds all metadata for a single property
 * 
 * @author Jhonatan Teixeira <jhonatan.teixeira@gmail.com>
 */
class PropertyMetadata extends BaseMetadata
{
    use AnnotationsTrait;
    
    public $type;
    
    public $typeInfo;

    public $setter;

    public $getter;

    /**
     * @param ReflectionClass $class
     * @param string $name
     */
    public function __construct($class, $name)
    {
        parent::__construct($class, $name);
        
        $this->type     = $this->parseType() ?? $this->getTypedPorpertyType();
        $this->typeInfo = $this->parseTypeDecoration($this->type);
    }
    
    private function getTypedPorpertyType() {
        $type = $this->reflection->getType();
        
        if ($type) {
            return $type->getName();
        }
    }
    
    public function getValue($obj)
    {
        if ($obj instanceof AccessInterceptorValueHolderInterface) {
            $obj = $obj->getWrappedValueHolderValue();
        }
        
        return parent::getValue($obj);
    }
    
    public function setValue($obj, $value)
    {
        if ($obj instanceof AccessInterceptorValueHolderInterface) {
            $obj = $obj->getWrappedValueHolderValue();
        }
        
        parent::setValue($obj, $value);
    }
    
    private function parseType()
    {
        $docComment = $this->reflection->getDocComment();

        preg_match(
            '/@var\s+(?P<full>(?P<class>[^\[\s<]+)(?P<suffix>(\[\])|(<.+>))?)/',
            $docComment,
            $matches
        );

        $fullType = $matches['full'] ?? null;
        $type     = $matches['class'] ?? null;

        if (null === $type) {
            return;
        }

        if ($resolvedType = $this->resolveFullTypeName($type, $matches['suffix'] ?? null)) {
            return $resolvedType;
        }

        return $fullType;
    }

    private function resolveFullTypeName($type, $suffix = null) {
        $type = preg_replace('/^\?/', '', $type);

        if (preg_match('/^\\\/', $type)) {
            return preg_replace('/^\\\/', '', $type) . $suffix;
        }

        $uses = $this->getClassUses();
        $type = str_replace('\\', '\\\\', $type);

        foreach ($uses as $use) {
            if (preg_match("/{$type}$/", $use)) {
                return $use . $suffix;
            }

            if (class_exists("$use\\$type")) {
                return "$use\\$type" . $suffix;
            }
        }
    }
    
    private function getClassUses(): array
    {
        $filename = $this->reflection->getDeclaringClass()->getFileName();
        
        if (is_file($filename)) {
            $contents = file_get_contents($filename);
            
            preg_match_all('/use\s+(.*);/', $contents, $matches);
            
            $uses = $matches[1] ?? [];
            
            $matches = [];
            
            preg_match('/namespace\s+(.*);/', $contents, $matches);
            
            if (!empty($matches[1])) {
                array_push($uses, $matches[1]);
            }
            
            return $uses;
        }
        
        return [];
    }
    
    public function getParsedType()
    {
        if (isset($this->type)) {
            return preg_replace('/(\[\]$)|(\<\>$)/', '', $this->type);
        }
    }
    
    public function isNativeType(): bool
    {
        return in_array($this->type, [
            'string',
            'array',
            'int',
            'integer',
            'float',
            'boolean',
            'bool',
            'DateTime',
            '\DateTime',
            '\DateTimeImmutable',
            'DateTimeImmutable',
        ]);
    }
    
    public function isDecoratedType(): bool
    {
        return (bool) preg_match('/(.*)((\<(.*)\>)|(\[\]))/', $this->type);
    }
    
    public function isDateType(): bool
    {
        $type = $this->isDecoratedType() ? $this->typeInfo['class'] ?? $this->type : $this->type;
        
        return in_array($type, ['DateTime', '\DateTime', 'DateTimeImmutable', '\DateTimeImmutable']);
    }
    
    private function parseTypeDecoration(string $type = null)
    {
        if (preg_match('/(?P<class>.*)((\<(?P<decoration>.*)\>)|(?P<brackets>\[\]))/', $type, $matches)) {
            $decoration = isset($matches['brackets']) ? $matches['class'] : $matches['decoration'];

            return [
                'class'      => isset($matches['brackets'])
                    ? 'array'
                    : $this->resolveFullTypeName($matches['class']) ?? $matches['class'],
                'decoration' => $this->resolveFullTypeName($decoration) ?? $decoration
            ];
        }
    }

    public function hasSetter() {
        return !empty($this->setter);
    }
    
    public function hasGetter() {
        return !empty($this->getter);
    }

    public function serialize()
    {
        return serialize([
            $this->class,
            $this->name,
            $this->annotations,
            $this->type,
            $this->typeInfo,
            $this->setter,
            $this->getter,
        ]);
    }

    public function unserialize($str)
    {
        [
            $this->class,
            $this->name,
            $this->annotations,
            $this->type,
            $this->typeInfo,
            $this->setter,
            $this->getter,
        ] = unserialize($str);

        $this->reflection = new \ReflectionProperty($this->class, $this->name);
        $this->reflection->setAccessible(true);
    }
}
