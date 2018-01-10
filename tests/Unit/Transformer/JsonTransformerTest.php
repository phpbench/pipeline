<?php

namespace PhpBench\Pipeline\Tests\Unit\Transformer;

use PHPUnit\Framework\TestCase;
use PhpBench\Pipeline\Tests\StepTestCase;
use PhpBench\Pipeline\Transformer\JsonTransformer;

class JsonTransformerTest extends StepTestCase
{
    public function testTransform()
    {
        $result = $this->runStep(new JsonTransformer(), [ [ 1, 2 ] ]);
        $this->assertEquals(['[1,2]' . PHP_EOL], $result);
    }

    public function testTransformPretty()
    {
        $result = $this->runStep(new JsonTransformer(true), [ [ 1, 2 ] ]);
        $this->assertEquals([<<<'EOT'
[
    1,
    2
]

EOT
        ], $result);
    }
}
