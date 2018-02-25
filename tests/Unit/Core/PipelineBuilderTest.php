<?php

namespace PhpBench\Pipeline\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use PhpBench\Pipeline\Core\PipelineBuilder;
use PhpBench\Pipeline\Core\StageFactory;
use PhpBench\Pipeline\Core\Pipeline;
use PhpBench\Pipeline\Core\Stage;
use PhpBench\Pipeline\Core\Schema;
use Generator;
use stdClass;
use PhpBench\Pipeline\Core\Exception\InvalidStageType;

class PipelineBuilderTest extends TestCase
{
    /**
     * @var ObjectProphecy
     */
    private $factory;

    /**
     * @var PipelineBuilder
     */
    private $builder;

    /**
     * @var ObjectProphecy
     */
    private $stage;

    public function setUp()
    {
        $this->factory = $this->prophesize(StageFactory::class);
        $this->builder = new PipelineBuilder(
            $this->factory->reveal()
        );
    }

    public function testBuildsAnEmptyPipeline()
    {
        $pipeline = $this->builder->build();
        $this->assertInstanceOf(Pipeline::class, $pipeline);
    }

    public function testSingleStagePipeline()
    {
        $this->factory->create('foobar')->willReturn(
            new class implements Stage {
                public function configure(Schema $schema)
                {
                }

                public function __invoke(array $config = []): Generator
                {
                    yield 'hello';
                }
            }
        );

        $pipeline = $this->builder
            ->stage('foobar')
            ->build();

        $this->assertInstanceOf(Pipeline::class, $pipeline);
        $this->assertCount(1, $pipeline);
    }

    public function testMultiStagePipeline()
    {
        $this->factory->create('foobar')->willReturn(
            new class implements Stage {
                public function configure(Schema $schema)
                {
                }

                public function __invoke(array $config = []): Generator
                {
                    yield 'hello';
                }
            }
        );

        $pipeline = $this->builder
            ->stage('foobar')
            ->stage('foobar')
            ->build();

        $this->assertInstanceOf(Pipeline::class, $pipeline);
        $this->assertCount(2, $pipeline);
    }

    public function testAppliesSchemaToConfiguration()
    {
        $this->factory->create('foobar')->willReturn(
            new class implements Stage {
                public function configure(Schema $schema)
                {
                    $schema->setDefaults([
                        'foobar' => 'Hello',
                    ]);
                }

                public function __invoke(array $config = []): Generator
                {
                    yield;
                    yield $config;
                }
            }
        );

        $result = $this->builder
            ->stage('foobar')
            ->build()
            ->run();

        $this->assertEquals([ 'foobar' => 'Hello' ], $result);
    }

    public function testPassesConfigurationToStages()
    {
        $this->factory->create('foobar')->willReturn(
            new class implements Stage {
                public function configure(Schema $schema)
                {
                    $schema->setDefaults([
                        'foobar' => 'Hello',
                    ]);
                }

                public function __invoke(array $config = []): Generator
                {
                    yield;
                    yield $config;
                }
            }
        );

        $result = $this->builder
            ->stage('foobar', [
                'foobar' => 'Goodbye',
            ])
            ->build()
            ->run();

        $this->assertEquals([ 'foobar' => 'Goodbye' ], $result);
    }

    public function testAcceptsACallableAsAType()
    {
        $result = $this->builder
            ->stage(function () {
                yield;
                yield [ 'foobar' => 'Goodbye' ];
            })
            ->build()
            ->run();

        $this->assertEquals([ 'foobar' => 'Goodbye' ], $result);
    }

    public function testExceptionOnInvalidStageType()
    {
        $this->expectException(InvalidStageType::class);

        $result = $this->builder
            ->stage(new stdClass())
            ->build();

        $this->assertEquals([ 'foobar' => 'Goodbye' ], $result);
    }
}
