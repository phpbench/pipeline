<?php

namespace PhpBench\Pipeline\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use PhpBench\Pipeline\Bridge\Native\ClassStageFactory;
use PhpBench\Pipeline\Core\PipelineBuilder;

class StageTestCase extends TestCase
{
    public function builder(): PipelineBuilder
    {
        return new PipelineBuilder(
            new ClassStageFactory()
        );
    }

}
