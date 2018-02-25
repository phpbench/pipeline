<?php

namespace PhpBench\Pipeline\Tests\Unit\Config;

use PHPUnit\Framework\TestCase;
use PhpBench\Pipeline\Pipeline;
use Generator;
use PhpBench\Pipeline\Exception\StageMustBeCallable;
use PhpBench\Pipeline\Exception\StageMustCreateGenerator;

class PipelineTest extends TestCase
{
    public function testSingleValueStep()
    {
        $pipeline = new Pipeline([
            new class {
                function __invoke(): Generator 
                { 
                    $data = yield;
                    $data[] = 'hello';
                    yield $data;
                }
            }
        ]);

        $result = $pipeline->run();
        $this->assertEquals(['hello'], $result);
    }

    public function testMultiValueStep()
    {
        $pipeline = new Pipeline([
            new class {
                function __invoke(): Generator 
                { 
                    $data = yield;
                    $data[] = 'hello';
                    $data = yield $data;
                    $data[] = 'goodbye';
                    yield $data;
                }
            }
        ]);

        $result = $pipeline->run();
        $this->assertEquals(['hello', 'goodbye'], $result);
    }

    public function testMultiStage()
    {
        $pipeline = new Pipeline([
            new class {
                function __invoke(): Generator 
                { 
                    $data = yield;
                    $data[] = 'hello';
                    yield $data;
                }
            },
            new class {
                function __invoke(): Generator 
                { 
                    $data = yield;
                    $data[] = 'goodbye';
                    yield $data;
                }
            }
        ]);

        $result = $pipeline->run();
        $this->assertEquals(['hello', 'goodbye'], $result);
    }

    public function testMultiStageTake()
    {
        $pipeline = new Pipeline([
            new class {
                function __invoke(): Generator 
                { 
                    $data = [];
                    while (true) {
                        $data = yield $data;
                        $data[] = 'hello';
                    }
                }
            },
            new class {
                function __invoke(): Generator 
                {
                    $data = [];
                    for ($i = 0; $i <= 3; $i++) {
                        $data = yield $data;
                    }
                }
            }
        ]);

        $result = $pipeline->run();
        $this->assertEquals(['hello', 'hello', 'hello'], $result);
    }

    public function testNestedPipeline()
    {
        $pipeline = new Pipeline([
            new class {
                function __invoke(): Generator 
                { 
                    yield;
                    yield [ 'one' ];
                }
            },
            new Pipeline([
                new class {
                    function __invoke(): Generator 
                    { 
                        $data = yield;
                        $data[] = 'two';
                        yield $data;
                    }
                },
                new class {
                    function __invoke(): Generator 
                    { 
                        $data = yield;
                        $data[] = 'three';
                        yield $data;
                    }
                },
            ]),
            new class {
                function __invoke(): Generator 
                { 
                    $data = yield;
                    $data[] = 'four';
                    yield $data;
                }
            },
        ]);

        $result = $pipeline->run();
        $this->assertEquals(['one', 'two', 'three', 'four'], $result);
    }

    public function testPrimesInput()
    {
        $pipeline = new Pipeline([
            new class {
                function __invoke(): Generator 
                { 
                    $data = yield;
                    $data[] = 1;
                    yield $data;
                }
            },
        ]);
        $result = $pipeline->run([ 0 ]);
        $this->assertEquals([0, 1], $result);
    }

    public function testThrowsExceptionForNotCallableStage()
    {
        $this->expectException(StageMustBeCallable::class);
        $this->expectExceptionMessage('Stage must be a callable');

        $pipeline = new Pipeline([[]]);
    }

    public function testThrowsExceptionWhenStageNotYieldingAGenerator()
    {
        $this->expectException(StageMustCreateGenerator::class);
        $this->expectExceptionMessage('Callable stage must return a Generator');

        $pipeline = new Pipeline([ function () { return [ 'asd' ]; } ]);
    }

    public function testOuroborosFibonacci()
    {
        $pipeline = new Pipeline([
            function () {
                $data = yield;
                for ($i = 0; $i < 11; $i++) {
                    $data = yield $data;
                }
            },
            function () {
                $data = yield;
                while (true) {
                    $data[] = array_sum(array_slice($data, -2));
                    $data = yield $data;
                }
            },
        ]);
        $result = $pipeline->run([0, 1]);
        $this->assertEquals([0, 1, 1, 2, 3, 5, 8, 13, 21, 34, 55, 89, 144], $result);
    }
}
