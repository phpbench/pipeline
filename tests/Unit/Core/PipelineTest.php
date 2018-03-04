<?php

namespace PhpBench\Pipeline\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use PhpBench\Pipeline\Core\GeneratorFactory;
use PhpBench\Pipeline\Core\Pipeline;
use stdClass;
use PhpBench\Pipeline\Core\Exception\InvalidStage;
use PhpBench\Pipeline\Core\Exception\InvalidArgumentException;
use PhpBench\Pipeline\Core\Exception\InvalidYieldedValue;

class PipelineTest extends TestCase
{
    /**
     * @var GeneratorFactory|ObjectProphecy
     */
    private $factory;

    public function setUp()
    {
        $this->factory = $this->prophesize(GeneratorFactory::class);
    }

    public function testRunsAnEmptyPipeline()
    {
        $data = $this->runPipeline([]);
        $this->assertEquals([], $data);
    }

    public function testPipesToCallableStage()
    {
        $data = $this->runPipeline([
            function () {
                $data = yield;
                $data[] = 'Goodbye';
                yield $data;
            },
        ], ['Hello']);
        $this->assertEquals(['Hello', 'Goodbye'], $data);
    }

    public function testThrowsExceptionIfStageNotStageOrCallable()
    {
        $this->expectException(InvalidStage::class);
        $this->expectExceptionMessage('Stage must either be a callable or a stage alias, got "stdClass"');
        $this->runPipeline([
            new stdClass()
        ]);
    }

    public function testInvalidStageArity()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Stage must be at least a 1 and at most a 2 element array ([ (string) stage-name, (array) stage-config ], got 3 elements');

        $this->runPipeline([
            [ 'foobar', ['barfoo' => 'asd' ], 'googoo' ],
        ]);
    }

    public function testThrowsAnExceptionIfStageDoesNotYieldAnArray()
    {
        $this->expectException(InvalidYieldedValue::class);

        $this->runPipeline([
            function () {
                yield;
                yield 'string';
            },
        ]);
    }

    private function runPipeline(array $stages, array $initial = [])
    {
        $pipeline = new Pipeline();

        $generator = $pipeline([
            'stages' => $stages,
            'initial_value' => $initial,
            'generator_factory' => $this->factory->reveal()
        ]);

        foreach ($generator as $data) {
        }

        return $data;
    }
}
