<?php

namespace PhpBench\Framework\Tests\Unit\Parameters;

use PhpBench\Framework\Tests\Unit\StepTestCase;
use PhpBench\Framework\Parameters\FixedParameters;

class FixedParametersTest extends StepTestCase
{
    public function testParameters()
    {
        $results = $this->runStep(new FixedParameters([
            'hello' => 'goodbye',
        ]), [
            [
                'one' => 'two'
            ],
        ]);

        $this->assertEquals([
            [
                'hello' => 'goodbye',
                'one' => 'two',
            ],
        ], $results);
    }

    public function testParametersWithScalar()
    {
        $results = $this->runStep(new FixedParameters([
            'hello' => 'goodbye',
        ]), [ 'scalar' ]);

        $this->assertEquals([
            [
                'hello' => 'goodbye',
                0 => 'scalar',
            ]
        ], $results);
    }

    public function testParametersWithNull()
    {
        $results = $this->runStep(new FixedParameters([
            'hello' => 'goodbye',
        ]), [ null ]);

        $this->assertEquals([
            [
                'hello' => 'goodbye',
            ]
        ], $results);
    }
}
