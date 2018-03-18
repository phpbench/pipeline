<?php

namespace PhpBench\Pipeline\Core;

use PhpBench\Pipeline\Core\Exception\InvalidStage;
use PhpBench\Pipeline\Core\Exception\InvalidYieldedValue;
use Generator;

class GeneratorFactory
{
    /**
     * @var StageRegistry
     */
    private $registry;

    public function __construct(StageRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function generatorFor($stage, array $config = []): ConfiguredGenerator
    {
        if (is_callable($stage)) {
            $generator = $stage();

            if (false === $generator instanceof Generator) {
                throw new InvalidYieldedValue(sprintf(
                    'Callable stages must return Generators, got "%s"',
                    is_object($generator) ? get_class($generator) : gettype($generator)
                ));
            }

            return new ConfiguredGenerator($generator, []);
        }

        if (is_string($stage)) {
            $stage = (array) $stage;
        }

        if (false === is_array($stage)) {
            throw new InvalidStage(sprintf(
                'Stage must either be a stage config element or a callable, got "%s"',
                is_object($stage) ? get_class($stage) : gettype($stage)
            ));
        }

        if (false === isset($stage[0])) {
            throw new InvalidStage(sprintf(
                'Stage config element must be a 1 to 2 element tuple (e.g. ["stage\/alias",{"config1":"value1"}]), got "%s"',
                json_encode($stage)
            ));
        }

        switch (count($stage)) {
            case 1:
                list($stage) = $stage;
                return $this->generatorForAliasAndConfig($stage);
            case 2:
                list($stage, $config) = $stage;
                return $this->generatorForAliasAndConfig($stage, $config);
            default:
                throw new InvalidStage(sprintf(
                    'Stage config element cannot have more than 2 elements, got %s',
                    count($stage)
                ));
        }
    }

    private function generatorForAliasAndConfig($stage, array $config = []): ConfiguredGenerator
    {
        $stage = $this->registry->get($stage);
        $schema = new Schema();
        $stage->configure($schema);

        if ($stage instanceof RequiresGeneratorFactory) {
            $config['generator_factory'] = $this;
        }

        $config = $schema->resolve($config);

        return new ConfiguredGenerator($stage->__invoke(), $config);
    }
}
