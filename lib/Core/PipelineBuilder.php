<?php

namespace PhpBench\Pipeline\Core;

use PhpBench\Pipeline\Extension\Console\ConsoleExtension;
use Generator;
use PhpBench\Pipeline\Extension\Core\CoreExtension;
use PhpBench\Pipeline\Core\Exception\InvalidStage;

final class PipelineBuilder
{
    /**
     * @var array
     */
    private $stages = [];

    /**
     * @var array
     */
    private $extensions = [];

    private function __construct()
    {
        $this->extensions[] = new Pipeline();
    }

    public static function create(): self
    {
        return new self();
    }

    public static function createWithDefaults(): self
    {
        $builder = self::create();
        $builder->addExtension(new CoreExtension());
        $builder->addExtension(new ConsoleExtension());

        return $builder;
    }

    public function build(): BuiltPipeline
    {
        $registry = new StageRegistry(
            $this->extensions
        );
        $generatorFactory = new GeneratorFactory($registry);

        return new BuiltPipeline($this->stages, $generatorFactory);
    }

    public function load(array $stages): self
    {
        foreach ($stages as $stage) {
        }

        return $this;
    }

    /**
     * @var callable|Stage
     */
    public function stage($stage, array $config = []): self
    {
        if (is_string($stage)) {
            $this->stages[] = [$stage, $config];

            return $this;
        }

        $this->stages[] = $stage;

        return $this;
    }

    public function addExtension(PipelineExtension $extension)
    {
        $this->extensions[] = $extension;

        return $this;
    }

    public function run(array $initialValue = []): array
    {
        return $this->build()->run($initialValue);
    }

    public function generator(array $initialData = []): Generator
    {
        $generator = $this->build()->generator($initialData);

        return $generator;
    }
}
