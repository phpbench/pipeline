<?php

namespace PhpBench\Pipeline\Extension\Console;

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
use PhpBench\Pipeline\Extension\Console\Stage\Redraw;

class ConsoleExtension implements PipelineExtension
{
    /**
     * @var array
     */
    private $stages;

    public function __construct()
    {
        $this->stages = [
            'console/redraw' => new Redraw(),
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
