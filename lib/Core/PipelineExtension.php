<?php

namespace PhpBench\Pipeline\Core;

interface PipelineExtension
{
    public function stageAliases(): array;

    public function stage(string $alias): Stage;
}
