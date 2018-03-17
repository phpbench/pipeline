<?php

namespace PhpBench\Pipeline\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use PhpBench\Pipeline\Core\GeneratorFactory;
use PhpBench\Pipeline\Core\Pipeline;
use stdClass;
use PhpBench\Pipeline\Core\Exception\InvalidStage;
use PhpBench\Pipeline\Core\Exception\InvalidArgumentException;
use PhpBench\Pipeline\Core\Exception\InvalidYieldedValue;
use PhpBench\Pipeline\Core\ConfiguredGenerator;

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
                list($config, $data) = yield;
                $data[] = 'Goodbye';
                yield $data;
            },
        ], ['Hello']);
        $this->assertEquals(['Hello', 'Goodbye'], $data);
    }

    public function testPipesToAConfiguredStageWithNoConfig()
    {
        $this->factory->generatorFor('test/foobar', [])->will(function () {
            return new ConfiguredGenerator((function () {
                list($config, $data) = yield;
                $data[] = 'Goodbye';
                yield $data;
            })(), []);
        });
        $data = $this->runPipeline([
            ['test/foobar'],
        ], ['Hello']);
        $this->assertEquals(['Hello', 'Goodbye'], $data);
    }

    public function testPipesToAConfiguredStageWithConfig()
    {
        $this->factory->generatorFor('test/foobar', [
            'key' => 'value',
        ])->will(function () {
            return new ConfiguredGenerator((function () {
                list($config, $data) = yield;
                $data[] = 'Goodbye';
                yield $data;
            })(), []);
        });
        $data = $this->runPipeline([
            ['test/foobar', ['key' => 'value']],
        ], ['Hello']);
        $this->assertEquals(['Hello', 'Goodbye'], $data);
    }

    public function testThrowsExceptionIfStageNotStageOrCallable()
    {
        $this->expectException(InvalidStage::class);
        $this->expectExceptionMessage('Stage must either be a callable or a stage alias, got "stdClass"');
        $this->runPipeline([
            new stdClass(),
        ]);
    }

    public function testInvalidStageArity()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Stage must be at least a 1 and at most a 2 element array ([ (string) stage-name, (array) stage-config ], got 3 elements');

        $this->runPipeline([
            ['foobar', ['barfoo' => 'asd'], 'googoo'],
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

    public function testCanEnableFeedback()
    {
        $stage = function () {
            list($config, $data) = yield;

            for ($i = 0; $i < 2; ++$i) {
                $data[] = 'Hello';
                list($config, $data) = yield $data;
            }
        };

        $result = $this->runPipeline([
            $stage,
            $stage,
        ], [], true);

        $this->assertEquals([
            'Hello', 'Hello', 'Hello', 'Hello',
        ], $result);
    }

    private function runPipeline(array $stages, array $data = [], bool $feedback = false)
    {
        $generator = (new Pipeline())();

        $return = $data;
        while ($generator->valid()) {
            $data = $generator->send([[
                'stages' => $stages,
                'generator_factory' => $this->factory->reveal(),
                'feedback' => $feedback,
            ], $data]);

            if (null === $data) {
                break;
            }

            $return = $data;
        }

        return $return;
    }
}
