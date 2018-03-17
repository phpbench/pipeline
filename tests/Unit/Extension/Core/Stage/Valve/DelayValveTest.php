<?php

namespace PhpBench\Pipeline\Tests\Unit\Extension\Core\Stage\Valve;

use PhpBench\Pipeline\Tests\Unit\Extension\Core\CoreTestCase;

class DelayValveTest extends CoreTestCase
{
    public function testDelaysExecution()
    {
        $start = microtime(true);
        $this->pipeline()
            ->stage('valve/delay', [ 'time' => 10000 ])
            ->generator()
            ->current();

        $end = microtime(true);

        $this->assertGreaterThan(10000, ($end - $start) * 1E6);
    }
}
