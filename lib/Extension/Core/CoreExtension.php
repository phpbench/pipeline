<?php

namespace PhpBench\Pipeline\Extension\Core;

use PhpBench\Pipeline\Core\PipelineExtension;
use PhpBench\Pipeline\Core\Stage;
use PhpBench\Pipeline\Extension\Core\Stage\Encoder\JsonEncoder;
use PhpBench\Pipeline\Extension\Core\Stage\Sampler\CallableSampler;
use PhpBench\Pipeline\Extension\Core\Stage\Output\StreamOutput;
use PhpBench\Pipeline\Extension\Core\Stage\Parameter\SerialParameter;
use PhpBench\Pipeline\Extension\Core\Stage\Valve\TakeValve;
use PhpBench\Pipeline\Extension\Core\Stage\Sampler\CurlSampler;
use PhpBench\Pipeline\Extension\Core\Stage\Filter\KeysFilter;

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
            'filter/keys' => new KeysFilter(),
            'output/stream' => new StreamOutput(),
            'parameter/serial' => new SerialParameter(),
            'sampler/callable' => new CallableSampler(),
            'sampler/curl' => new CurlSampler(),
            'valve/take' => new TakeValve(),
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
