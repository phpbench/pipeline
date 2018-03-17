<?php

namespace PhpBench\Pipeline\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use PhpBench\Pipeline\Core\Exception\UnknownStage;
use PhpBench\Pipeline\Core\PipelineExtension;
use PhpBench\Pipeline\Core\StageRegistry;
use PhpBench\Pipeline\Core\Exception\StageAliasAlreadyRegistered;
use PhpBench\Pipeline\Core\Stage;

class StageRegistryTest extends TestCase
{
    /**
     * @var PipelineExtension|ObjectProphecy
     */
    private $extension1;

    /**
     * @var PipelineExtension|ObjectProphecy
     */
    private $extension2;

    /**
     * @var Stage|ObjectProphecy
     */
    private $stage;

    public function setUp()
    {
        $this->extension1 = $this->prophesize(PipelineExtension::class);
        $this->extension2 = $this->prophesize(PipelineExtension::class);
        $this->stage = $this->prophesize(Stage::class);
    }

    public function testThrowsExceptionIfStageIsNotRegisteredAndNoExtensions()
    {
        $this->expectException(UnknownStage::class);
        $this->expectExceptionMessage('Stage "foobar" is not registered, registered stages: ""');

        $registry = $this->createRegistry([]);
        $registry->get('foobar');
    }

    public function testNotFoundExceptionShowsAvailableStages()
    {
        $this->expectException(UnknownStage::class);
        $this->expectExceptionMessage('Stage "foobar" is not registered, registered stages: "stage1", "stage2", "stage3", "stage4"');

        $this->extension1->stageAliases()->willReturn(['stage1', 'stage2']);
        $this->extension2->stageAliases()->willReturn(['stage3', 'stage4']);

        $registry = $this->createRegistry([
            $this->extension1->reveal(),
            $this->extension2->reveal(),
        ]);

        $registry->get('foobar');
    }

    public function testExtensionIfStageAliasIsAlreadyRegistered()
    {
        $this->expectException(StageAliasAlreadyRegistered::class);
        $this->expectExceptionMessage(sprintf(
            'Stage "foobar" is already registered by "%s" (when adding "%s")',
            get_class($this->extension1->reveal()),
            get_class($this->extension2->reveal())
        ));

        $this->extension1->stageAliases()->willReturn(['foobar']);
        $this->extension2->stageAliases()->willReturn(['foobar']);

        $registry = $this->createRegistry([
            $this->extension1->reveal(),
            $this->extension2->reveal(),
        ]);

        $registry->get('foobar');
    }

    public function testReturnsStage()
    {
        $this->extension1->stageAliases()->willReturn(['foobar']);

        $registry = $this->createRegistry([
            $this->extension1->reveal(),
        ]);
        $this->extension1->stage('foobar')->willReturn($this->stage->reveal());

        $stage = $registry->get('foobar');
        $this->assertSame($this->stage->reveal(), $stage);
    }

    private function createRegistry(array $extensions)
    {
        return new StageRegistry($extensions);
    }
}
