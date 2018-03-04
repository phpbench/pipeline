<?php

namespace PhpBench\Pipeline\Core;

use PhpBench\Pipeline\Core\PipelineExtension;
use PhpBench\Pipeline\Core\StageRegistry;
use PhpBench\Pipeline\Core\GeneratorFactory;
use PhpBench\Pipeline\Core\Pipeline;

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

    public static function create(): PipelineBuilder
    {
        return new self();
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
}
