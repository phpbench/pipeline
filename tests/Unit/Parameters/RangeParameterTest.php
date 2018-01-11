<?php

namespace PhpBench\Pipeline\Tests\Unit\Parameters;

use PhpBench\Pipeline\Tests\StepTestCase;
use PhpBench\Pipeline\Parameters\RangeParameter;

class RangeParameterTest extends StepTestCase
{
    /**
     * @dataProvider provideRange
     */
    public function testRange($start, $end, $step, array $expected)
    {
        $results = $this->runStep(new RangeParameter('range', $start, $end, $step), array_fill(0, count($expected), null));
        $this->assertEquals($expected, $results);
    }

    public function provideRange()
    {
        return [
            'Integer' => [
                0, 4, 1,
                [
                    [ 'range' => 0, ],
                    [ 'range' => 1, ],
                    [ 'range' => 2, ],
                    [ 'range' => 3, ],
                    [ 'range' => 4, ],
                ]
            ],
            'Float' => [
                0, 1, 0.2,
                [
                    [ 'range' => 0, ],
                    [ 'range' => 0.2, ],
                    [ 'range' => 0.4,  ],
                    [ 'range' => 0.6, ],
                    [ 'range' => 0.8, ],
                    [ 'range' => 1 ],
                ]
            ],
            'Step greater than range' => [
                0, 1, 2,
                [
                    [ 'range' => 0, ],
                ]
            ],
            'Negative' => [
                0, -1, 0.5,
                [
                    [ 'range' => 0, ],
                    [ 'range' => -0.5, ],
                    [ 'range' => -1, ],
                ]
            ],
            'Same start and end' => [
                0, 0, 0.5,
                [
                    [ 'range' => 0, ],
                ]
            ],
        ];
    }
}
