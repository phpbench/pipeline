<?php

namespace PhpBench\Pipeline\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use PhpBench\Pipeline\Core\PipelineExtension;
use PhpBench\Pipeline\Core\PipelineBuilder;
use PhpBench\Pipeline\Core\BuiltPipeline;
use PhpBench\Pipeline\Core\Stage;
use Prophecy\Argument;
use PhpBench\Pipeline\Core\Schema;

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
    }

    public function testBuildsAnEmptyPipeline()
    {
        $builder = PipelineBuilder::create();
        $pipeline = $builder->build();
        $this->assertInstanceOf(BuiltPipeline::class, $pipeline);
    }

    public function testBuildsPipelineWithCallable()
    {
        $builder = PipelineBuilder::create();
        $builder->stage(function () {
            yield;
            yield [ 'Hello' ];
        });
        $pipeline = $builder->build();

        $this->assertInstanceOf(BuiltPipeline::class, $pipeline);
        $result = $pipeline->run();
        $this->assertEquals(['Hello'], $result);
    }

    public function testBuildsPipelineWithStageAlias()
    {
        $this->extension1->stageAliases()->willReturn(['test/foobar']);
        $this->extension1->stage('test/foobar')->willReturn($this->stage1->reveal());
        $this->stage1->configure(Argument::type(Schema::class))->will(function () {});
        $this->stage1->__invoke([])->will(function () {
            yield;
            yield [ 'Test' ];
        });

        $builder = PipelineBuilder::create();
        $builder->addExtension($this->extension1->reveal());

        $builder->stage('test/foobar');
        $pipeline = $builder->build();

        $this->assertInstanceOf(BuiltPipeline::class, $pipeline);
        $result = $pipeline->run();
        $this->assertEquals(['Test'], $result);
    }

    public function testBuildsPipelineWithMultipleStages()
    {
        $builder = PipelineBuilder::create();
        $builder->stage(function () {
            yield;
            yield [ 'Hello' ];
        });
        $builder->stage(function () {
            $data = yield;
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
            yield [ 'Hello' ];
        });
        $result = $builder->run();

        $this->assertEquals(['Hello'], $result);
    }
}
