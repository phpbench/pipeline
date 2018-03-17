<?php

namespace PhpBench\Pipeline\Core;

use Generator;
use PhpBench\Pipeline\Core\Exception\InvalidStage;
use PhpBench\Pipeline\Core\Exception\InvalidArgumentException;
use PhpBench\Pipeline\Core\Exception\InvalidYieldedValue;
use PhpBench\Pipeline\Core\ConfiguredGenerator;

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
                $generatorConfig = $configuredGenerator->config();
                $generatorConfig = $this->replaceTokens($generatorConfig, $data);
                $data = $configuredGenerator->generator()->send([$generatorConfig, $data]);

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

    private function replaceTokens(array $generatorConfig, $data): array
    {
        foreach ($generatorConfig as $key => &$value) {
            if (false === is_scalar($value)) {
                continue;
            }

            if (0 === preg_match_all('{%(.+?)%}', $value, $matches)) {
                continue;
            }

            $keys = array_unique($matches[1]);
            $replacements = [];

            foreach ($keys as $index => $key) {
                if (false === isset($data[$key])) {
                    throw new InvalidArgumentException(sprintf(
                        'Data does not contain key for token "%s", data keys: "%s"',
                        $key, implode('", "', array_keys($data))
                    ));
                }

                $replacements['%' . $key . '%'] = $data[$key];
            }

            $value = strtr($value, $replacements);
        }

        return $generatorConfig;
    }
}
