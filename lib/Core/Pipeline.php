<?php

namespace PhpBench\Pipeline\Core;

use PhpBench\Pipeline\Core\Stage;
use Generator;
use PhpBench\Pipeline\Core\Exception\InvalidStage;
use PhpBench\Pipeline\Core\PipelineExtension;
use PhpBench\Pipeline\Core\Exception\InvalidArgumentException;
use PhpBench\Pipeline\Core\Exception\InvalidYieldedValue;

class Pipeline implements Stage, PipelineExtension
{
    public function __invoke(array $config): Generator
    {
        $data = yield;

        $generators = [];
        foreach ($config['stages'] as $stage) {
            if (is_callable($stage)) {
                $generators[] = $stage();
                continue;
            }

            if (is_array($stage)) {
                if (count($stage) != 2) {
                    throw new InvalidArgumentException(sprintf(
                        'Stage must be at least a 1 and at most a 2 element array ([ (string) stage-name, (array) stage-config ], got %s elements',
                        count($stage)
                    ));
                }

                list($stageName, $stageConfig) = $stage;
                $generators[] = $config['generator_factory']->generatorFor($stageName, $stageConfig);
                continue;
            }

            throw new InvalidStage(sprintf(
                'Stage must either be a callable or a stage alias, got "%s"',
                is_object($stage) ? get_class($stage) : gettype($stage)
            ));
        }

        $data = $config['initial_value'];

        if (empty($generators)) {
            yield $data;
            return $data;
        }

        while (true) {
            foreach ($generators as $generator) {
                $data = $generator->send($data);

                if (!$generator->valid()) {
                    break 2;
                }

                if (false === is_array($data)) {
                    throw new InvalidYieldedValue(sprintf(
                        'All yielded values must bne arrays, got "%s"',
                        gettype($data)
                    ));
                }
            }

            yield $data;
        }

        return $data;
    }

    public function configure(Schema $schema)
    {
        $schema->setRequired([
            'generator_factory'
        ]);

        $schema->setTypes([
            'generator_factory' => GeneratorFactory::class,
            'initial_value' => 'array',
            'stages' => 'array',
        ]);

        $schema->setDefaults([
            'stages' => [],
            'initial_value' => [],
        ]);
    }

    public function stageAliases(): array
    {
        return [ 'pipeline' ];
    }

    public function stage(string $alias): Stage
    {
        return new self();
    }
}
