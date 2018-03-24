<?php

namespace PhpBench\Pipeline\Core;

use Generator;
use PhpBench\Pipeline\Core\Exception\InvalidStage;
use PhpBench\Pipeline\Core\Exception\InvalidArgumentException;
use PhpBench\Pipeline\Core\Exception\InvalidYieldedValue;
use PhpBench\Pipeline\Core\RequiresGeneratorFactory;

class Pipeline implements Stage, PipelineExtension, RequiresGeneratorFactory
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
            foreach ($configuredGenerators as $configuredGenerator) {
                $generatorConfig = $configuredGenerator->config();
                $generatorConfig = $this->replaceTokens($generatorConfig, $data);

                $response = $configuredGenerator->generator()->send([$generatorConfig, $data]);

                if (false === $configuredGenerator->generator()->valid()) {
                    break 2;
                }

                if (false === $response instanceof Signal && false === is_array($response)) {
                    throw new InvalidYieldedValue(sprintf(
                        'All yielded values must be arrays or Signals, got "%s"',
                        gettype($data)
                    ));
                }

                if ($response instanceof Signal) {
                    switch ($response) {
                        case Signal::continue():
                            break 2;
                    }
                }

                $data = $response;
            }

            list($config, $data) = yield $data;
        }
    }

    private function buildGenerators(array $config): array
    {
        $generators = [];
        foreach ($config['stages'] as $stage) {
            $generators[] = $config['generator_factory']->generatorFor($stage);
        }

        return $generators;
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
        ]);

        $schema->setDefaults([
            'stages' => [],
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

                $replacements['%'.$key.'%'] = $data[$key];
            }

            $value = strtr($value, $replacements);
        }

        return $generatorConfig;
    }
}
