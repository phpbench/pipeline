<?php

namespace PhpBench\Pipeline\Tests\Unit\Config;

use PHPUnit\Framework\TestCase;
use PhpBench\Pipeline\Core\Pipeline;
use Generator;
use PhpBench\Pipeline\Exception\StageMustBeCallable;
use PhpBench\Pipeline\Exception\StageMustCreateGenerator;
use PhpBench\Pipeline\Core\Stage;
use Countable;
use PhpBench\Pipeline\Core\Exception\GeneratorMustYieldAnArray;

class PipelineTest extends TestCase
{
    public function testReturnsNullWithNoGenerators()
    {
        $pipeline = new Pipeline([]);
        $result = $pipeline->run();

        $this->assertEmpty($result);
    }

    public function testConnectsGenerator()
    {
        $pipeline = new Pipeline([
            (function (): Generator {
                $data = yield;
                yield $data;
            })()
        ]);

        $result = $pipeline->run(['Hello']);

        $this->assertEquals(['Hello'], $result);
    }

    public function testChainsGenerators()
    {
        $pipeline = new Pipeline([
            (function (): Generator {
                $data = yield;
                $data[] = 'goodbye';
                yield $data;
            })(),
            (function (): Generator {
                $data = yield;
                yield $data;
            })()
        ]);

        $result = $pipeline->run(['Hello']);

        $this->assertEquals(['Hello', 'goodbye'], $result);
    }

    public function testActsAsAStage()
    {
        $pipeline = new Pipeline([]);
        $this->assertInstanceOf(Stage::class, $pipeline);
    }

    public function testIsCountable()
    {
        $pipeline = new Pipeline([
            (function (): Generator {
                yield [ 'hello' ]; 
            })(),
            (function (): Generator {
                yield [ 'hello' ]; 
            })()
        ]);
        $this->assertInstanceOf(Countable::class, $pipeline);
        $this->assertCount(2, $pipeline);
    }

    public function testExceptionIfYieldedValueIsNotAnArray()
    {
        $this->expectException(GeneratorMustYieldAnArray::class);

        $pipeline = new Pipeline([
            (function (): Generator {
                yield;
                yield 'hello'; 
            })()
        ]);
        $pipeline->run();
    }
}
