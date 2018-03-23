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
use PhpBench\Pipeline\Extension\Core\Stage\Aggregator\DescribeAggregator;
use PhpBench\Pipeline\Extension\Core\Stage\Parameter\CounterParameter;
use PhpBench\Pipeline\Extension\Core\Stage\Valve\DelayValve;
use PhpBench\Pipeline\Extension\Core\Stage\Valve\TimeoutValve;
use PhpBench\Pipeline\Extension\Core\Stage\Aggregator\CollectorAggregator;
use PhpBench\Pipeline\Extension\Core\Stage\Distribution\Fork;

class CoreExtension implements PipelineExtension
{
    /**
     * @var array
     */
    private $stages;

    public function __construct()
    {
        $this->stages = [
            'aggregator/collector' => new CollectorAggregator(),
            'aggregator/describe' => new DescribeAggregator(),
            'encoder/json' => new JsonEncoder(),
            'filter/keys' => new KeysFilter(),
            'output/stream' => new StreamOutput(),
            'parameter/serial' => new SerialParameter(),
            'parameter/counter' => new CounterParameter(),
            'sampler/callable' => new CallableSampler(),
            'sampler/curl' => new CurlSampler(),
            'valve/delay' => new DelayValve(),
            'valve/take' => new TakeValve(),
            'valve/timeout' => new TimeoutValve(),
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
