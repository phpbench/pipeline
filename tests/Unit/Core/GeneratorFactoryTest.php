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
        $this->stage1->__invoke([])->will(function () {
            yield;
        });
        $generator = $this->factory->generatorFor('foobar', []);
        $this->assertInstanceOf(Generator::class, $generator);
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

        $this->stage1->__invoke([
            'foo' => 'bar',
            'bar' => 'six',
        ])->will(function () {
            yield;
        });
        $generator = $this->factory->generatorFor('foobar', [
            'bar' => 'six',
        ]);
        $this->assertInstanceOf(Generator::class, $generator);
    }
}
