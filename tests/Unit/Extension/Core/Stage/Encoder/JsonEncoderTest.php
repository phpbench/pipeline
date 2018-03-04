<?php

namespace PhpBench\Pipeline\Tests\Unit\Extension\Core\Stage\Encoder;

use PhpBench\Pipeline\Tests\Unit\Core\StageTestCase;
use PhpBench\Pipeline\Extension\Core\Stage\Encoder\JsonEncoder;

class JsonEncoderTest extends StageTestCase
{
    public function testEncodesToJson()
    {
        $result = $this->builder()
            ->stage(function () {
                yield;
                yield [ 'two' => 'three' ];
            })
            ->stage(JsonEncoder::class)
            ->build()()->send([]);

        $this->assertEquals(['{"two":"three"}'], $result);
    }

    public function testPrettyPrintsJson()
    {
        $result = $this->builder()
            ->stage(function () {
                yield;
                yield [ 'two' => 'three' ];
            })
            ->stage(JsonEncoder::class, [
                'pretty' => true,
            ])
            ->build()()->send([]);

        $this->assertEquals([<<<'EOT'
{
    "two": "three"
}
EOT
        ], $result);
    }
}
