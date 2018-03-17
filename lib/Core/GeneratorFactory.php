<?php

namespace PhpBench\Pipeline\Core;

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

    public function generatorFor(string $stageName, array $config): ConfiguredGenerator
    {
        $stage = $this->registry->get($stageName);
        $schema = new Schema();
        $stage->configure($schema);
        $config = $schema->resolve($config);

        return new ConfiguredGenerator($stage->__invoke(), $config);
    }
}
