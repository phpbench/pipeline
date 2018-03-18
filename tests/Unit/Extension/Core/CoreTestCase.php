<?php

namespace PhpBench\Pipeline\Tests\Unit\Extension\Core;

use PhpBench\Pipeline\Core\PipelineBuilder;
use PhpBench\Pipeline\Extension\Core\CoreExtension;
use PhpBench\Pipeline\Tests\Unit\PipelineTestCase;

class CoreTestCase extends PipelineTestCase
{
    protected function pipeline(): PipelineBuilder
    {
        return PipelineBuilder::create()
            ->addExtension(new CoreExtension());
    }
}
