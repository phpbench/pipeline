<?php

namespace PhpBench\Pipeline\Tests\Unit\Extension\Core\Stage\Output;

use PhpBench\Pipeline\Core\Stage;
use PhpBench\Pipeline\Tests\Unit\Extension\Core\CoreTestCase;

class StreamOutputTest extends CoreTestCase
{
    public function testWritesToStream()
    {
        $result = $this->pipeline()
            ->stage('output/stream', ['stream' => 'php://temp'])
            ->generator()->send(['hello']);

        $this->assertEquals(['hello'], $result);
    }
}
