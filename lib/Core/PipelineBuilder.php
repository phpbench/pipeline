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
            if (is_callable($stage) || is_string($stage)) {
                $this->stage($stage);
                continue;
            }

            if (is_array($stage)) {
                if (false === isset($stage[0])) {
                    throw new InvalidStage(sprintf(
                        'Stage config element must be a 1 to 2 element tuple (e.g. ["stage\/alias",{"config1":"value1"}]), got "%s"',
                        json_encode($stage)
                    ));
                }

                switch (count($stage)) {
                    case 1:
                        list($stage) = $stage;
                        $this->stage($stage);
                        continue 2;
                    case 2:
                        list($stage, $config) = $stage;
                        $this->stage($stage, $config);
                        continue 2;
                    default:
                        throw new InvalidStage(sprintf(
                            'Stage config element cannot have more than 2 elements, got %s',
                            count($stage)
                        ));
                }
            }

            throw new InvalidStage(sprintf(
                'Stage must either be an array config element or a callable, got "%s"',
                is_object($stage) ? get_class($stage) : gettype($stage)
            ));
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
