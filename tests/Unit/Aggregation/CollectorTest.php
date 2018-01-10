<?php

namespace PhpBench\Pipeline\Tests\Unit\Aggregation;

use PhpBench\Pipeline\Tests\StepTestCase;
use PhpBench\Pipeline\Aggregation\Collector;

class CollectorTest extends StepTestCase
{
    public function testEmpty()
    {
        $results = $this->runStep(new Collector(), []);
        $this->assertEquals([], $results);
    }

    public function testNull()
    {
        $results = $this->runStep(new Collector(), [ null ]);
        $this->assertEquals([ [ null ] ], $results);
    }

    public function testScalars()
    {
        $results = $this->runStep(new Collector(), [ 'hello', 'world' ]);
        $this->assertEquals([ [ 'hello' ], ['hello', 'world' ] ], $results);
    }
}
