<?php

namespace PhpBench\Framework\Sampler;

use PhpBench\Framework\Step;
use Generator;
use PhpBench\Framework\Pipeline;
use Closure;
use PhpBench\Framework\Util\StepConfig;
use PhpBench\Framework\Exception\AssertionFailure;

class CallbackSampler implements Step
{
    /**
     * @var Closure
     */
    private $callback;

    /**
     * @var array
     */
    private $config;

    /**
     * @var string
     */
    private $label;

    public function __construct(array $config)
    {
        $config = StepConfig::resolve([
            'revs' => 1,
            'label' => 'Callback',
            'callback' => function () {}
        ], $config, get_class($this));

        if ($config['revs'] < 1) {
            throw new AssertionFailure(sprintf(
                '`revs` must be a positive integer, got "%s"',
                $config['revs']
            ));
        }

        $this->label = $config['label'];
        $this->callback = $config['callback'];
        $this->revs = $config['revs'];
    }

    public function generator(Pipeline $pipeline): Generator
    {
        foreach ($pipeline->pop() as $data) {
            yield [
                'label' => $this->label,
                'parameters' => $data,
                'microseconds' => $this->time($data)
            ];
        }
    }

    private function time($data)
    {
        $callback = $this->callback;

        if (1 === $this->revs) {
            return $this->executeSingleMeasurement($callback, $data);
        }

        return $this->executeCompositeMeasurement($callback, $data);
    }

    private function executeCompositeMeasurement(Closure $callback, $data)
    {
        $start = microtime(true);
        for ($i = 0; $i < $this->revs; $i++) {
            $callback($data);
        }
        $end = microtime(true);

        return (($end * 1E6) - ($start * 1E6)) / $this->revs;
    }

    private function executeSingleMeasurement(Closure $callback, $data)
    {
        $start = microtime(true);
        $callback($data);
        $end = microtime(true);

        return ($end * 1E6) - ($start * 1E6);
    }
}
