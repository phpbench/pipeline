<?php

namespace PhpBench\Pipeline\Tests\Unit\Transformer;

use PHPUnit\Framework\TestCase;
use PhpBench\Pipeline\Tests\StepTestCase;
use PhpBench\Pipeline\Transformer\BarGraphTransformer;

class BarGraphTransformerTest extends StepTestCase
{
    /**
     * @dataProvider provideBar
     */
    public function testBar(int $width, array $data, string $expected)
    {
        $results = $this->runStep(new BarGraphTransformer('label', 'time', $width), [$data]);
        $result = array_pop($results);

        $this->assertEquals($expected, $result);
    }

    public function provideBar()
    {
        return [
            [
                10,
                [
                    [
                        'label' => 'One',
                        'time' => 10,
                    ],
                    [
                        'label' => 'Two',
                        'time' => 5,
                    ],
                ],
                <<<'EOT'
One  |██████████ 10
Two  |█████      5

EOT
            ],
            [
                5,
                [
                    [
                        'label' => 'One',
                        'time' => 10,
                    ],
                    [
                        'label' => 'Two',
                        'time' => 9,
                    ],
                    [
                        'label' => 'The',
                        'time' => 9.5,
                    ],
                ],
                <<<'EOT'
One  |█████ 10
Two  |████▌ 9
The  |████▊ 9.5

EOT
            ],
            [
                40,
                [
                    [
                        'label' => '1',
                        'time' => 1,
                    ],
                    [
                        'label' => '2',
                        'time' => 1.5,
                    ],
                    [
                        'label' => '3',
                        'time' => 1.75,
                    ],
                    [
                        'label' => 4,
                        'time' => 2,
                    ],
                ],
                <<<'EOT'
1  |████████████████████                     1
2  |██████████████████████████████           1.5
3  |███████████████████████████████████      1.75
4  |████████████████████████████████████████ 2

EOT
            ]
        ];
    }
}
