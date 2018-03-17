<?php

namespace PhpBench\Pipeline\Core;

use Countable;
use PhpBench\Pipeline\Core\GeneratorFactory;
use Generator;

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

    public function run(array $data = []): array
    {
        $generator = $this->generator();
        $return = $data;
        while ($generator->valid()) {
            $data = $generator->send($data);

            if (null === $data) {
                break;
            }

            $return = $data;
        }

        return $return;
    }

    public function generator(): Generator
    {
        $generator = $this->factory->generatorFor('pipeline', [
            'stages' => $this->stages,
            'generator_factory' => $this->factory
        ]);

        return $generator;
    }
    
}
