<?php

namespace PhpBench\Framework\Sampler;

use PhpBench\Framework\Step;
use Generator;
use PhpBench\Framework\Pipeline;
use Closure;
use PhpBench\Framework\Util\StepConfig;
use PhpBench\Framework\Exception\AssertionFailure;
use PhpBench\Framework\Config\ConfigBuilder;

class CallbackSampler implements Step
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(array $config)
    {
        $this->config = ConfigBuilder::create(__CLASS__)
            ->defaults([
                'revs' => 1,
                'label' => 'Callback',
                'callback' => function () {}
            ])
            ->build($config);
    }

    public function generator(Pipeline $pipeline): Generator
    {
        foreach ($pipeline->pop() as $data) {
            yield [
                'label' => $this->config->resolve('label', $data),
                'parameters' => $data,
                'microseconds' => $this->time($data)
            ];
        }
    }

    private function time($data)
    {
        $callback = $this->config['callback'];
        $revs = (int) $this->config->resolve('revs', $data);

        if (1 === $revs) {
            return $this->executeSingleMeasurement($callback, $data);
        }

        return $this->executeCompositeMeasurement($revs, $callback, $data);
    }

    private function executeCompositeMeasurement(int $revs, Closure $callback, $data)
    {
        $start = microtime(true);
        $revs = $revs;
        for ($i = 0; $i < $revs; $i++) {
            $callback($data);
        }
        $end = microtime(true);

        return (($end * 1E6) - ($start * 1E6)) / $revs;
    }

    private function executeSingleMeasurement(Closure $callback, $data)
    {
        $start = microtime(true);
        $callback($data);
        $end = microtime(true);

        return ($end * 1E6) - ($start * 1E6);
    }
}
