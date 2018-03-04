<?php

namespace PhpBench\Pipeline\Tests\Unit\Extension\Core\Stage\Output;

use PhpBench\Pipeline\Tests\Unit\Core\StageTestCase;
use PhpBench\Pipeline\Extension\Core\Stage\Output\StreamOutput;
use Closure;
use PhpBench\Pipeline\Core\Stage;

class StreamOutputTest extends StageTestCase
{
    public function testWritesToStream()
    {
        $result = ($this->builder()
            ->stage(function () {
                yield;
                yield [ 'hello' ];
            })
            ->stage(StreamOutput::class, [
                'stream' => 'php://temp'
            ])
            ->build())()->send([]);

        $this->assertEquals(['hello'], $result);
    }
}
