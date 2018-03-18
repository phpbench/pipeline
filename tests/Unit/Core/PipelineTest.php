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
        $this->factory->generatorFor(Argument::type('callable'))->will(function (array $args) {
            return new ConfiguredGenerator($args[0](), []);
        });

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
        $this->factory->generatorFor(['test/foobar'])->will(function () {
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
        $this->setUpFactory(['test/foobar', [ 'key' => 'value' ]], (function () {
                list($config, $data) = yield;
                $data[] = 'Goodbye';
                yield $data;
        })(), [ 'key' => 'value' ]);

        $data = $this->runPipeline([
            ['test/foobar', ['key' => 'value']],
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
            ['test/foobar', ['key' => $configValue]],
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

        $this->factory->generatorFor(Argument::type('callable'))->will(function ($args) {
            return new ConfiguredGenerator($args[0](), []);
        });

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

    private function setUpFactory($stage, Generator $generator, array $resolvedConfig = [])
    {
        $this->factory->generatorFor($stage)
            ->willReturn(
                new ConfiguredGenerator($generator, $resolvedConfig)
            );
    }
}
