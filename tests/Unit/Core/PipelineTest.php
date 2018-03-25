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
use Prophecy\Argument;
use Generator;
use PhpBench\Pipeline\Core\Schema;

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
        $data = $this->runPipeline([
            'stages' => [],
        ]);
        $this->assertEquals([], $data);
    }

    public function testPipesToCallableStage()
    {
        $this->factory->generatorFor(Argument::type('callable'))->will(function (array $args) {
            return new ConfiguredGenerator($args[0](), []);
        });

        $data = $this->runPipeline([
            'stages' => [
                function () {
                    list($config, $data) = yield;
                    $data[] = 'Goodbye';
                    yield $data;
                },
            ],
        ], ['Hello']);

        $this->assertEquals(['Hello', 'Goodbye'], $data);
    }

    public function testPipesToAConfiguredStageWithNoConfig()
    {
        $this->factory->generatorFor(['test/foobar'])->will(function () {
            return new ConfiguredGenerator((function () {
                list($config, $data) = yield;
                $data[] = 'Goodbye';
                yield $data;
            })(), []);
        });
        $data = $this->runPipeline([
            'stages' => [
                ['test/foobar'],
            ],
        ], ['Hello']);
        $this->assertEquals(['Hello', 'Goodbye'], $data);
    }

    public function testPipesToAConfiguredStageWithConfig()
    {
        $this->setUpFactory(['test/foobar', [ 'key' => 'value' ]], (function () {
                list($config, $data) = yield;
                $data[] = 'Goodbye';
                yield $data;
        })(), [ 'key' => 'value' ]);

        $data = $this->runPipeline([
            'stages' => [
                ['test/foobar', ['key' => 'value']],
            ],
        ], ['Hello']);
        $this->assertEquals(['Hello', 'Goodbye'], $data);
    }

    /**
     * @dataProvider provideSubstitutesConfigTokensWithDataValues
     */
    public function testSubstitutesConfig($configValue, array $data, array $expected, array $exception = [])
    {
        if ($exception) {
            list($expectedClass, $expectedMessage) = $exception;
            $this->expectException($expectedClass);
            $this->expectExceptionMessage($expectedMessage);
        }

        $this->setUpFactory(['test/foobar', [ 'key' => $configValue ]], (function () {
                list($config, $data) = yield;
                yield [$config['key']];
        })(), [ 'key' => $configValue ]);

        $data = $this->runPipeline([
            'stages' => [
                ['test/foobar', ['key' => $configValue]],
            ],
        ], $data);

        $this->assertEquals($expected, $data);
    }

    public function provideSubstitutesConfigTokensWithDataValues()
    {
        yield 'token only' => [
            '%my_value%',
            ['my_value' => 'Hai!'],
            ['Hai!'],
        ];

        yield 'token with surrounding text' => [
            'hello - %my_value% - bye',
            ['my_value' => 'Hai!'],
            ['hello - Hai! - bye'],
        ];

        yield 'multiple same tokens' => [
            '%my_value% - %my_value%',
            ['my_value' => 'Hai!'],
            ['Hai! - Hai!'],
        ];

        yield 'multiple different tokens' => [
            '%my_value% - %my.foobar%',
            ['my_value' => 'Hai!', 'my.foobar' => 'Ciao!'],
            ['Hai! - Ciao!'],
        ];

        yield 'token only' => [
            '%my_value%',
            ['my_value' => 'Hai!'],
            ['Hai!'],
        ];

        yield 'throws exception if data does not contain the token' => [
            '%my_value% - %my.foobar%',
            ['my_value' => 'Hai!'],
            [],
            [
                InvalidArgumentException::class,
                'Data does not contain key for token "my.foobar", data keys: "my_value"',
            ],
        ];
    }

    public function testThrowsAnExceptionIfStageDoesNotYieldAnArray()
    {
        $this->factory->generatorFor(Argument::type('callable'))->will(function ($args) {
            return new ConfiguredGenerator($args[0](), []);
        });

        $this->expectException(InvalidYieldedValue::class);

        $this->runPipeline([
            'stages' => [
                function () {
                    yield;
                    yield 'string';
                }
            ],
        ]);
    }

    public function testCopiesInputToStagesWhenForkIsEnabled()
    {
        $this->factory->generatorFor(Argument::type('callable'))->will(function ($args) {
            return new ConfiguredGenerator($args[0](), []);
        });

        $data = $this->runPipeline([
            'fork' => true,
            'stages' => [
                function () use (&$stage1) {
                    list($config, $data) = yield;
                    $stage1 = $data;
                    yield [ 'Two' ];
                },
                function () use (&$stage2) {
                    list($config, $data) = yield;
                    $stage2 = $data;
                    yield [ 'Three' ];
                },
            ],
        ], ['One']);

        $this->assertEquals(['Two', 'Three'], $data, 'Results are merged');
        $this->assertEquals(['One'], $stage1, 'Data is copied to stage 1');
        $this->assertEquals(['One'], $stage2, 'Data is copied to stage 2');
    }

    private function runPipeline(array $config, array $data = [])
    {
        $config['generator_factory'] = $this->factory->reveal();

        $pipeline = new Pipeline();
        $schema = new Schema();
        $pipeline->configure($schema);
        $config = $schema->resolve($config);
        $generator = $pipeline();

        $data = $generator->send([ $config, $data ]);

        return $data;
    }

    private function setUpFactory($stage, Generator $generator, array $resolvedConfig = [])
    {
        $this->factory->generatorFor($stage)
            ->willReturn(
                new ConfiguredGenerator($generator, $resolvedConfig)
            );
    }
}
