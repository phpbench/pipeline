<?php

namespace PhpBench\Framework\Sampler;

use PhpBench\Framework\Step;
use Generator;
use PhpBench\Framework\Pipeline;
use Closure;
use PhpBench\Framework\Util\StepConfig;

class CallbackSampler implements Step
{
    /**
     * @var Closure
     */
    private $callback;

    public function __construct(array $config)
    {
        $config = StepConfig::resolve([
            'label' => 'Callback',
            'callback' => function () {}
        ], $config, get_class($this));

        $this->label = $config['label'];
        $this->callback = $config['callback'];
    }

    public function generator(Pipeline $pipeline): Generator
    {
        $callback = $this->callback;

        foreach ($pipeline->pop() as $data) {
            $start = microtime(true);
            $callback($data);
            $end = microtime(true);

            yield [
                'label' => $this->label,
                'parameters' => $data,
                'microseconds' => ($end * 1E6) - ($start * 1E6)
            ];
        }
    }
}
