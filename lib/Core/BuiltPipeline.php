<?php

namespace PhpBench\Pipeline\Core;

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
        foreach ($this->generator($data) as $data) {
        }

        return $data;
    }

    public function generator(array $data = []): Generator
    {
        $configuredGenerator = $this->factory->generatorFor('pipeline', [
            'stages' => $this->stages,
        ]);

        $return = $data;
        $generator = $configuredGenerator->generator();

        while ($generator->valid()) {
            $data = $generator->send([$configuredGenerator->config(), $data]);

            if (null === $data) {
                break;
            }

            yield $data;
        }
    }
}
