<?php

namespace PhpBench\Framework\Tests\Unit\Transformer;

use PHPUnit\Framework\TestCase;
use PhpBench\Framework\Tests\Unit\StepTestCase;
use PhpBench\Framework\Transformer\JsonTransformer;

class JsonTransformerTest extends StepTestCase
{
    public function testTransform()
    {
        $result = $this->runStep(new JsonTransformer(), [ [ 1, 2 ] ]);
        $this->assertEquals('[1,2]', $result);
    }
}
