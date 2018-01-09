<?php

namespace PhpBench\Framework\Tests\Unit\Gate;

use PhpBench\Framework\Tests\Unit\StepTestCase;
use PhpBench\Framework\Gate\Delay;

class DelayTest extends StepTestCase
{
    public function testDelay()
    {
        $this->runStep(new Delay(100), []);
    }
}
