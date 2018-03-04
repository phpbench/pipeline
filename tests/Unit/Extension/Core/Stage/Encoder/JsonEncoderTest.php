<?php

namespace PhpBench\Pipeline\Tests\Unit\Extension\Core\Stage\Encoder;

use PhpBench\Pipeline\Tests\Unit\StageTestCase;
use PhpBench\Pipeline\Extension\Core\Stage\Encoder\JsonEncoder;

class JsonEncoderTest extends StageTestCase
{
    public function testEncodesToJson()
    {
        $result = $this->runStage(new JsonEncoder(), [], [ 'one' => 'two' ]);
        $this->assertEquals(['{"one":"two"}'], $result);
    }

    public function testPrettyPrintsJson()
    {
        $result = $this->runStage(new JsonEncoder(), [
            'pretty' => true,
        ], [ 'one' => 'two' ]);

        $this->assertEquals([<<<'EOT'
{
    "one": "two"
}
EOT
        ], $result);
    }
}
