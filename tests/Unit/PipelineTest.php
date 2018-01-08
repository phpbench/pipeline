<?php

namespace PhpBench\Framework\Tests\Unit;

use PHPUnit\Framework\TestCase;
use PhpBench\Framework\Step;
use PhpBench\Framework\Pipeline;
use PhpBench\Framework\Exception\EmptyPipeline;

class PipelineTest extends TestCase
{
    /**
     * @var Step|ObjectProphecy
     */
    private $step;

    public function setUp()
    {
        $this->step = $this->prophesize(Step::class);
    }

    public function testPop()
    {
        $pipeline = new Pipeline([
            $this->step->reveal()
        ]);

        $step = $pipeline->pop();

        $this->assertSame($this->step->reveal(), $step);
    }

    public function testPopOnEmpty()
    {
        $this->expectException(EmptyPipeline::class);
        $pipeline = new Pipeline([]);
        $pipeline->pop();
    }
}
