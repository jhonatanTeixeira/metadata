<?php

namespace Vox\Metadata\Test;

use PHPUnit\Framework\TestCase;
use Vox\Metadata\ParamMetadata;

function testFunction(string $param1) {

}

class ParamMetadataTest extends TestCase
{
    public function testShouldAcceptFunctionOrCallable() {
        $paramMetadata = new ParamMetadata(null, 'Vox\Metadata\Test\testFunction', 'param1');

        $this->assertEquals('string', $paramMetadata->type);

        $paramMetadata = new ParamMetadata(null, fn(string $a) => $a, 'a');
        $this->assertEquals('string', $paramMetadata->type);
    }
}
