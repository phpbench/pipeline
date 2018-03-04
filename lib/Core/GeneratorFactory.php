<?php

namespace PhpBench\Pipeline\Core;

use Generator;
use PhpBench\Pipeline\Core\Schema;

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

    public function generatorFor(string $stageName, array $config): Generator
    {
        $stage = $this->registry->get($stageName);
        $schema = new Schema();
        $stage->configure($schema);
        $config = $schema->resolve($config);

        return $stage->__invoke($config);
    }
}
