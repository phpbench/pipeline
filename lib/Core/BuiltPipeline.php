<?php

namespace PhpBench\Pipeline\Core;

use Countable;
use PhpBench\Pipeline\Core\GeneratorFactory;

final class BuiltPipeline
{
    /**
     * @var array
     */
    private $stages;

    /**
     * @var GeneratorFactory
     */
    private $factory;

    public function __construct(array $stages, GeneratorFactory $factory)
    {
        $this->stages = $stages;
        $this->factory = $factory;
    }

    public function run(array $initialValue = []): array
    {
        $generator = $this->factory->generatorFor('pipeline', [
            'stages' => $this->stages,
            'initial_value' => $initialValue,
            'generator_factory' => $this->factory
        ]);

        foreach ($generator as $data) {
        }

        return $data;
    }
}
