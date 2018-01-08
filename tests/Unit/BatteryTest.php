<?php

namespace PhpBench\Framework\Tests\Unit;

use PHPUnit\Framework\TestCase;
use PhpBench\Framework\Battery;
use PhpBench\Framework\Pipeline;

class BatteryTest extends TestCase
{
    public function testGenerate()
    {
        $pipeline = $this->prophesize(Pipeline::class);

        $battery = new Battery();
        $generator = $battery->generator($pipeline->reveal());

        $this->assertNull($generator->current());
        $generator->next();
        $this->assertNull($generator->current());
        $generator->next();
        $this->assertNull($generator->current());
        // ∞
    }
}
