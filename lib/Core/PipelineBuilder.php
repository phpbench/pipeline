<?php

namespace PhpBench\Pipeline\Core;

use PhpBench\Pipeline\Core\PipelineExtension;
use PhpBench\Pipeline\Core\StageRegistry;
use PhpBench\Pipeline\Core\GeneratorFactory;
use PhpBench\Pipeline\Core\Pipeline;
use PhpBench\Pipeline\Extension\Core\CoreExtension;
use Generator;

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

    /**
     * @var callable|Stage
     */
    public function stage($stage, array $config = []): self
    {
        if (is_string($stage)) {
            $this->stages[] = [ $stage, $config ];
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

    public function generator(): Generator
    {
        $generator = $this->build()->generator();

        return $generator;
    }
}
