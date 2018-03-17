<?php

namespace PhpBench\Pipeline\Tests\Unit\Extension\Core\Stage\Encoder;

use PhpBench\Pipeline\Tests\Unit\Extension\Core\CoreTestCase;

class JsonEncoderTest extends CoreTestCase
{
    public function testEncodesToJson()
    {
        $result = $this->pipeline()
            ->stage('encoder/json', [])
            ->generator(['one' => 'two']);

        $this->assertEquals(['{"one":"two"}'], $result->current());
    }

    public function testPrettyPrintsJson()
    {
        $result = $this->pipeline()
            ->stage('encoder/json', [
            'pretty' => true,
            ])
            ->generator(['one' => 'two'])->current();

        $this->assertEquals([<<<'EOT'
{
    "one": "two"
}
EOT
        ], $result);
    }
}
