<?php

namespace PhpBench\Framework\Tests\Unit\Transformer;

use PHPUnit\Framework\TestCase;
use PhpBench\Framework\Tests\Unit\StepTestCase;
use PhpBench\Framework\Transformer\BarGraphTransformer;

class BarGraphTransformerTest extends StepTestCase
{
    public function testBar()
    {
        $results = $this->runStep(new BarGraphTransformer('label', 'time', 10), [
            [
                'One' => [
                    'label' => 'One',
                    'time' => 10,
                ],
                'Two' => [
                    'label' => 'Two',
                    'time' => 5,
                ],
            ]
        ]);
        $result = array_pop($results);

        $this->assertEquals(<<<'EOT'
One  |██████████| 10
Two  |████|      5

EOT
        , $result);
    }
}
