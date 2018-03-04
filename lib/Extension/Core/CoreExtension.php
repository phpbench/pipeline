<?php

namespace PhpBench\Pipeline\Extension\Core;

use PhpBench\Pipeline\Core\PipelineExtension;
use PhpBench\Pipeline\Core\Stage;
use PhpBench\Pipeline\Extension\Core\Stage\Encoder\JsonEncoder;
use PhpBench\Pipeline\Extension\Core\Stage\Sampler\CallableSampler;
use PhpBench\Pipeline\Extension\Core\Stage\Output\StreamOutput;
use PhpBench\Pipeline\Extension\Core\Stage\Parameter\SerialParameter;

class CoreExtension implements PipelineExtension
{
    /**
     * @var array
     */
    private $stages;

    public function __construct()
    {
        $this->stages = [
            'encoder/json' => new JsonEncoder(),
            'output/stream' => new StreamOutput(),
            'sampler/callable' => new CallableSampler(),
            'parameter/serial' => new SerialParameter(),
        ];
    }

    public function stageAliases(): array
    {
        return array_keys($this->stages);
    }

    public function stage(string $alias): Stage
    {
        return $this->stages[$alias];
    }
}
