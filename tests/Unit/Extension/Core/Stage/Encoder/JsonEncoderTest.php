<?php

namespace PhpBench\Pipeline\Tests\Unit\Extension\Core\Stage\Encoder;

use PhpBench\Pipeline\Tests\Unit\Extension\Core\CoreTestCase;

class JsonEncoderTest extends CoreTestCase
{
    public function testEncodesToJson()
    {
        $result = $this->pipeline()
            ->stage('encoder/json', [])
            ->generator();

        $this->assertEquals(['{"one":"two"}'], $result->send(['one' => 'two']));
    }

    public function testPrettyPrintsJson()
    {
        $result = $this->pipeline()
            ->stage('encoder/json', [
            'pretty' => true,
            ])
            ->generator()->send(['one' => 'two']);

        $this->assertEquals([<<<'EOT'
{
    "one": "two"
}
EOT
        ], $result);
    }
}
