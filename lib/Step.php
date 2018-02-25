<?php

namespace PhpBench\Pipeline;

interface Step
{
    public function __invoke(array $config): Generator;

    public function configSchema(): ConfigSchema;
}
