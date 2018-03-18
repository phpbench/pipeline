<?php

namespace PhpBench\Pipeline\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use PhpBench\Pipeline\Core\StageRegistry;
use PhpBench\Pipeline\Core\Stage;
use PhpBench\Pipeline\Core\GeneratorFactory;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Argument;
use PhpBench\Pipeline\Core\Schema;
use Generator;
use PhpBench\Pipeline\Core\ConfiguredGenerator;
use PhpBench\Pipeline\Core\Exception\InvalidStage;
use stdClass;
use PhpBench\Pipeline\Core\Exception\InvalidYieldedValue;

class GeneratorFactoryTest extends TestCase
{
    /**
     * @var StageRegistry|ObjectProphecy
     */
    private $registry;

    /**
     * @var GeneratorFactory
     */
    private $factory;

    /**
     * @var Stage|ObjectProphecy
     */
    private $stage1;

    public function setUp()
    {
        $this->registry = $this->prophesize(StageRegistry::class);

        $this->factory = new GeneratorFactory($this->registry->reveal());

        $this->stage1 = $this->prophesize(Stage::class);
    }

    public function testProducesAGeneratorFromTheGivenStageNameAndConfig()
    {
        $this->registry->get('foobar')->willReturn($this->stage1->reveal());

        $this->stage1->configure(Argument::type(Schema::class))->shouldBeCalled();
        $this->stage1->__invoke()->will(function () {
            yield;
        });
        $generator = $this->factory->generatorFor(['foobar', []]);
        $this->assertInstanceOf(ConfiguredGenerator::class, $generator);
        $this->assertInstanceOf(Generator::class, $generator->generator());
    }

    public function testResolvesConfig()
    {
        $this->registry->get('foobar')->willReturn($this->stage1->reveal());

        $this->stage1->configure(Argument::type(Schema::class))->will(function (array $args) {
            $schema = $args[0];
            $schema->setDefaults([
                'foo' => 'bar',
                'bar' => 'foo',
            ]);
        });

        $config = [];
        $this->stage1->__invoke()->will(function () use (&$config) {
            yield;
        });
        $configuredGenerator = $this->factory->generatorFor(['foobar', [
            'bar' => 'six',
        ]]);
        $this->assertInstanceOf(ConfiguredGenerator::class, $configuredGenerator);
        $this->assertInstanceOf(Generator::class, $configuredGenerator->generator());
        $this->assertEquals(['foo' => 'bar', 'bar' => 'six'], $configuredGenerator->config());
    }

    public function testThrowsExceptionIfCallableDoesntReturnAGenerator()
    {
        $this->expectException(InvalidYieldedValue::class);
        $this->expectExceptionMessage('Callable stages must return Generators, got "stdClass"');

        $this->factory->generatorFor(function () {
            return new stdClass();
        });
    }

    public function testThrowsExceptionIfStageNotStageOrCallable()
    {
        $this->expectException(InvalidStage::class);
        $this->expectExceptionMessage('Stage must either be a stage config element or a callable, got "stdClass"');

        $this->factory->generatorFor(new stdClass());
    }

    public function testThrowsExceptionOnInvalidTuple()
    {
        $this->expectException(InvalidStage::class);
        $this->expectExceptionMessage('Stage config element must be a 1 to 2 element tuple (e.g. ["stage\/alias",{"config1":"value1"}]), got "{"foobar":{"foo":"bar"}}"');

        $this->factory->generatorFor(['foobar' => [ 'foo' => 'bar' ]]);
    }

    public function testInvalidStageArity()
    {
        $this->expectException(InvalidStage::class);
        $this->expectExceptionMessage('Stage config element cannot have more than 2 elements, got 3');

        $this->factory->generatorFor(['asd', 'asd', 'asd']);
    }

}
