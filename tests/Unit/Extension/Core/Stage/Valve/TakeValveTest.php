<?php

namespace PhpBench\Pipeline\Tests\Unit\Extension\Core\Stage\Valve;

use PhpBench\Pipeline\Tests\Unit\Extension\Core\CoreTestCase;

class TakeValveTest extends CoreTestCase
{
    public function testTakesOne()
    {
        $result = $this->pipeline()
            ->stage('valve/take', ['quantity' => 1])
            ->run(['hello']);

        $this->assertEquals(['hello'], $result);
    }

    public function testTakeeThree()
    {
        $result = $this->pipeline()
            ->stage(function () {
                list($config, $data) = yield;
                while (true) {
                    $data[] = 'goodbye';
                    yield $data;
                }
            })
            ->stage('valve/take', ['quantity' => 3])
            ->run([]);

        $this->assertEquals(['goodbye', 'goodbye', 'goodbye'], $result);
    }
}
