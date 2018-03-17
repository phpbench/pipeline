<?php

namespace PhpBench\Pipeline\Tests\Unit\Extension\Core\Stage\Valve;

use PhpBench\Pipeline\Tests\Unit\Extension\Core\CoreTestCase;

class TimeoutValveTest extends CoreTestCase
{
    public function testTerminatesAfterAGivenNumberOfMicroseconds()
    {
        $start = microtime(true);
        $this->pipeline()
            ->stage('valve/timeout', ['time' => 100000])
            ->run();

        $time = (microtime(true) - $start) * 1E6;

        $this->assertGreaterThanOrEqual(100000, $time);
    }
}
