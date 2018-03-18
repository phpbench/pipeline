<?php

namespace PhpBench\Pipeline\Extension\Core\Stage\Distribution;

use PhpBench\Pipeline\Core\Stage;
use Generator;
use PhpBench\Pipeline\Core\Schema;
use PhpBench\Pipeline\Core\RequiresGeneratorFactory;
use PhpBench\Pipeline\Core\GeneratorFactory;

class Fork implements Stage, RequiresGeneratorFactory
{
    public function __invoke(): Generator
    {
        list($config, $data) = yield;

        /** @var GeneratorFactory $generatorFactory */
        $generatorFactory = $config['generator_factory'];

        $configuredGenerators = [];
        foreach ($config['stages'] as $stage) {
            $configuredGenerators[] = $generatorFactory->generatorFor($stage);
        }

        while (true) {
            foreach ($configuredGenerators as $configuredGenerator) {
                $configuredGenerator->generator()->send([$configuredGenerator->config(), $data]);
            }

            list($config, $data) = yield $data;
        }

    }

    public function configure(Schema $schema)
    {
        $schema->setDefaults([
            'stages' => []
        ]);
        $schema->setRequired([ 'generator_factory' ]);
    }
}
