<?php

namespace PhpBench\Pipeline\Tests\Unit\Extension\Core\Stage\Transform;

use PhpBench\Pipeline\Tests\Unit\Extension\Core\CoreTestCase;

class FlattenTest extends CoreTestCase
{
    /**
     * @dataProvider provideFlattensArray
     */
    public function testWill(array $input, array $expected, array $config = [])
    {
        $pipeline = $this->pipeline()
            ->stage('transform/flatten', $config);

        $output = $pipeline->generator($input)->current();
        $this->assertEquals($expected, $output);
    }

    public function provideFlattensArray()
    {
        yield 'do nothing when passed an empty array' => [
            [],
            []
        ];

        yield 'do nothing when passed an flat array' => [
            [
                'hello' => 'goodbye',
                'bonjour' => 'aurevoir',
            ],
            [
                'hello' => 'goodbye',
                'bonjour' => 'aurevoir',
            ]
        ];

        yield 'flatten an array one level deep' => [
            [
                'hello' => [
                    'goodbye' => 'ciao',
                ],
                'bonjour' => [
                    'salut' => 'aurevoir',
                    'bonsoir' => 'bonnuit',
                ]
            ],
            [
                'hello_goodbye' => 'ciao',
                'bonjour_salut' => 'aurevoir',
                'bonjour_bonsoir' => 'bonnuit',
            ],
        ];

        yield 'flatten an array two levels deep' => [
            [
                'hello' => [
                    'goodbye' => [
                        'ciao' => 'arrivederci',
                    ]
                ],
            ],
            [
                'hello_goodbye_ciao' => 'arrivederci',
            ],
        ];

        yield 'flatten an array at one level two levels deep' => [
            [
                'hello' => [
                    'goodbye' => [
                        'ciao' => 'arrivederci',
                    ]
                ],
                'goodbye' => [
                    'hello' => [
                        'ciao' => 'arrivederci',
                    ]
                ],
            ],
            [
                'hello' => [
                    'goodbye_ciao' => 'arrivederci',
                ],
                'goodbye' => [
                    'hello_ciao' => 'arrivederci',
                ]
            ],
            [
                'level' => 1,
            ],
        ];
    }
}
