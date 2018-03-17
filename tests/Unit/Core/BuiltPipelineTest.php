<?php

namespace PhpBench\Pipeline\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use PhpBench\Pipeline\Core\GeneratorFactory;
use PhpBench\Pipeline\Core\BuiltPipeline;
use PhpBench\Pipeline\Core\ConfiguredGenerator;

class BuiltPipelineTest extends TestCase
{
    /**
     * @var ObjectProphecy
     */
    private $factory;

    public function setUp()
    {
        $this->factory = $this->prophesize(GeneratorFactory::class);
    }

    public function testItShouldProvideAGenerator()
    {
        $pipeline = new BuiltPipeline([], $this->factory->reveal());
        $this->factory->generatorFor('pipeline', [
            'stages' => [],
            'generator_factory' => $this->factory->reveal(),
        ])->will(function () {
            return new ConfiguredGenerator((function () {
                yield;
                yield 'hello';
                yield 'goodbye';
            })(), []);
        });

        $results = [];
        foreach ($pipeline->generator() as $line) {
            $results[] = $line;
        }

        $this->assertEquals(['hello', 'goodbye'], $results);
    }
}
