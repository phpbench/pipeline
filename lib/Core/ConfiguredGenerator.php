<?php

namespace PhpBench\Pipeline\Core;

use Generator;

final class ConfiguredGenerator
{
    /**
     * @var Generator
     */
    private $generator;

    /**
     * @var array
     */
    private $config;

    public function __construct(Generator $generator, array $config)
    {
        $this->generator = $generator;
        $this->config = $config;
    }

    public function config(): array
    {
        return $this->config;
    }

    public function generator(): Generator
    {
        return $this->generator;
    }
}
