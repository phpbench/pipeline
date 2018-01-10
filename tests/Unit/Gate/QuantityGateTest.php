<?php

namespace PhpBench\Pipeline\Tests\Unit\Gate;

use PHPUnit\Framework\TestCase;
use PhpBench\Pipeline\Tests\StepTestCase;
use PhpBench\Pipeline\Gate\QuantityGate;
use PhpBench\Pipeline\Exception\AssertionFailure;

class QuantityGateTest extends StepTestCase
{
    public function testGate()
    {
        $result = $this->runStep(new QuantityGate(4), [ 1, 2, 3, 4, 5, 6 ]);
        $this->assertEquals([ 1, 2, 3, 4 ], $result);
    }

    public function testNegative()
    {
        $this->expectException(AssertionFailure::class);
        $this->expectExceptionMessage('Quantity must be a positive integer, got -1');
        new QuantityGate(-1);
    }
}
