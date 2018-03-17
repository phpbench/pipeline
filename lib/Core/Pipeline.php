<?php

namespace PhpBench\Pipeline\Core;

use Generator;
use PhpBench\Pipeline\Core\Exception\InvalidStage;
use PhpBench\Pipeline\Core\Exception\InvalidArgumentException;
use PhpBench\Pipeline\Core\Exception\InvalidYieldedValue;

class Pipeline implements Stage, PipelineExtension
{
    public function __invoke(): Generator
    {
        list($config, $data) = yield;
        $configuredGenerators = $this->buildGenerators($config);
        $initialData = $data = (array) $data;

        if (empty($configuredGenerators)) {
            yield $initialData;

            return;
        }

        while (true) {
            if (false === $config['feedback']) {
                $data = $initialData;
            }

            foreach ($configuredGenerators as $configuredGenerator) {
                $data = $configuredGenerator->generator()->send([$configuredGenerator->config(), $data]);

                if (false === $configuredGenerator->generator()->valid()) {
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
    }

    private function buildGenerators(array $config): array
    {
        $generators = [];
        foreach ($config['stages'] as $stage) {
            if (is_callable($stage)) {
                $generators[] = new ConfiguredGenerator($stage(), $config);
                continue;
            }

            if (is_array($stage)) {
                $generators[] = $this->buildGeneratorFromArray($stage, $config);
                continue;
            }

            throw new InvalidStage(sprintf(
                'Stage must either be a callable or a stage alias, got "%s"',
                is_object($stage) ? get_class($stage) : gettype($stage)
            ));
        }

        return $generators;
    }

    private function buildGeneratorFromArray(array $stage, array $config): ConfiguredGenerator
    {
        if (count($stage) > 2) {
            throw new InvalidArgumentException(sprintf(
                'Stage must be at least a 1 and at most a 2 element array ([ (string) stage-name, (array) stage-config ], got %s elements',
                count($stage)
            ));
        }

        $stageName = $stage[0];
        $stageConfig = isset($stage[1]) ? $stage[1] : [];
        $generator = $config['generator_factory']->generatorFor($stageName, $stageConfig);

        return $generator;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(Schema $schema)
    {
        $schema->setRequired([
            'generator_factory',
        ]);

        $schema->setTypes([
            'generator_factory' => GeneratorFactory::class,
            'stages' => 'array',
            'feedback' => 'boolean',
        ]);

        $schema->setDefaults([
            'stages' => [],
            'feedback' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function stageAliases(): array
    {
        return ['pipeline'];
    }

    /**
     * {@inheritdoc}
     */
    public function stage(string $alias): Stage
    {
        return new self();
    }
}
