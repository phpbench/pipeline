<?php

namespace PhpBench\Pipeline\Tests\Unit\Gate;

use PhpBench\Pipeline\Tests\StepTestCase;
use PhpBench\Pipeline\Gate\Delay;

class DelayTest extends StepTestCase
{
    public function testDelay()
    {
        $results = $this->runStep(new Delay(100), [ 'foo' ]);
        $this->assertEquals(['foo'], $results);
    }
}
