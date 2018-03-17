<?php

namespace PhpBench\Pipeline\Tests\Unit\Extension\Core;

use PHPUnit\Framework\TestCase;
use PhpBench\Pipeline\Core\PipelineBuilder;
use PhpBench\Pipeline\Extension\Core\CoreExtension;
use PhpBench\Pipeline\Extension\Console\ConsoleExtension;

class CoreTestCase extends TestCase
{
    protected function pipeline(): PipelineBuilder
    {
        return PipelineBuilder::create()
            ->addExtension(new CoreExtension());
    }
}
