<?php

namespace PhpBench\Pipeline\Core;

use PhpBench\Pipeline\Core\Pipeline;
use PhpBench\Pipeline\Core\StageFactory;
use PhpBench\Pipeline\Core\Schema;
use PhpBench\Pipeline\Core\Exception\InvalidStageType;
use Closure;

class PipelineBuilder
{
    /**
     * @var StageFactory
     */
    private $factory;

    /**
     * @var Stage[]
     */
    private $stages = [];

    public function __construct(StageFactory $factory)
    {
        $this->factory = $factory;
    }

    public function stage($typeOrCallable, array $config = [])
    {
        $this->stages[] = [ $typeOrCallable, $config ];

        return $this;
    }

    public function build(): Pipeline
    {
        $generators = [];
        foreach ($this->stages as $stage) {
            list($type, $config) = $stage;
            $schema = new Schema();

            if (is_string($type)) {
                $stage = $this->factory->create($type);
                $stage->configure($schema);
                $config = $schema->resolve($config);
                $generators[] = $stage($config);
                continue;
            }

            if ($type instanceof Closure) {
                $generators[] = call_user_func($type);
                continue;
            }

            throw new InvalidStageType($type);
        }

        return new Pipeline($generators);
    }

    public function run(): array
    {
        return $this->build()->run();
    }
}
