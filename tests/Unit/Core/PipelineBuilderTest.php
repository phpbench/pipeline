<?php

namespace PhpBench\Pipeline\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use PhpBench\Pipeline\Core\PipelineExtension;
use PhpBench\Pipeline\Core\PipelineBuilder;
use PhpBench\Pipeline\Core\BuiltPipeline;
use PhpBench\Pipeline\Core\Stage;
use Prophecy\Argument;
use PhpBench\Pipeline\Core\Schema;
use PhpBench\Pipeline\Core\Exception\InvalidStage;
use stdClass;

class PipelineBuilderTest extends TestCase
{
    /**
     * @var PipelineExtension|ObjectProphecy
     */
    private $extension1;

    /**
     * @var Stage|ObjectProphecy
     */
    private $stage1;

    public function setUp()
    {
        $this->extension1 = $this->prophesize(PipelineExtension::class);
        $this->stage1 = $this->prophesize(Stage::class);
        $this->stage2 = $this->prophesize(Stage::class);
    }

    public function testBuildsAnEmptyPipeline()
    {
        $builder = PipelineBuilder::create();
        $pipeline = $builder->build();
        $this->assertInstanceOf(BuiltPipeline::class, $pipeline);
    }

    /**
     * @dataProvider provideBuildsPipelinesFromStages
     */
    public function testBuildsPipelineFromStages(array $stages, array $expected, array $exception = [])
    {
        if ($exception) {
            list($class, $message) = $exception;
            $this->expectException($class);
            $this->expectExceptionMessage($message);
        }

        $this->extension1->stageAliases()->willReturn(['test/stage1', 'test/stage2']);
        $this->extension1->stage('test/stage1')->willReturn($this->stage1->reveal());
        $this->extension1->stage('test/stage2')->willReturn($this->stage2->reveal());

        $this->stage1->configure(Argument::type(Schema::class))->will(function ($args) {
            $schema = $args[0];
            $schema->setDefaults([ 'foo' => 'bar' ]);
        });
        $this->stage2->configure(Argument::type(Schema::class))->will(function ($args) {
            $schema = $args[0];
            $schema->setDefaults([ 'foo' => 'bar' ]);
        });
        $this->stage1->__invoke()->will(function () {
            yield;
            yield ['Value1'];
        });
        $this->stage2->__invoke()->will(function () {
            yield;
            yield ['Value2'];
        });

        $builder = PipelineBuilder::create();
        $builder->addExtension($this->extension1->reveal());
        $builder->load($stages);

        $pipeline = $builder->build();

        $this->assertInstanceOf(BuiltPipeline::class, $pipeline);
        $result = $pipeline->run();
        $this->assertEquals($expected, $result);
    }

    public function provideBuildsPipelinesFromStages()
    {
        yield 'with string alias' => [
            [ 
                'test/stage1' 
            ],
            [ 'Value1' ],
        ];
        yield 'with 2 string aliases' => [
            [ 
                'test/stage1',
                'test/stage2',
            ],
            [ 'Value2' ],
        ];
        yield 'with callable' => [
            [ 
                function () {
                    yield;
                    yield [ 'Hai!' ];
                },
            ],
            [ 'Hai!' ],

        ];
        yield 'with alias in an array' => [
            [ 
                [ 'test/stage1' ]
            ],
            [ 'Value1' ],
        ];
        yield 'with alias and config' => [
            [ 
                [ 'test/stage1', ['foo' => 'bar'] ]
            ],
            [ 'Value1' ],
        ];
    }

    public function testBuildsPipelineWithMultipleStages()
    {
        $builder = PipelineBuilder::create();
        $builder->stage(function () {
            yield;
            yield ['Hello'];
        });
        $builder->stage(function () {
            list($config, $data) = yield;
            $data[] = 'Goodbye';
            yield $data;
        });
        $pipeline = $builder->build();

        $this->assertInstanceOf(BuiltPipeline::class, $pipeline);
        $result = $pipeline->run();
        $this->assertEquals(['Hello', 'Goodbye'], $result);
    }

    public function testBuildsAndRunsThePipeline()
    {
        $builder = PipelineBuilder::create();
        $builder->stage(function () {
            yield;
            yield ['Hello'];
        });
        $result = $builder->run();

        $this->assertEquals(['Hello'], $result);
    }
}
