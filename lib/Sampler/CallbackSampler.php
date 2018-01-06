<?php

namespace PhpBench\Framework\Sampler;

use PhpBench\Framework\Step;
use Generator;
use SplQueue;
use Closure;
use PhpBench\Framework\TimeResult;

class CallbackSampler implements Step
{
    /**
     * @var Closure
     */
    private $closure;

    /**
     * @var string
     */
    private $label;

    public function __construct(string $label, Closure $closure)
    {
        $this->closure = $closure;
        $this->label = $label;
    }

    public function generate(SplQueue $queue): Generator
    {
        $callback = $this->closure;

        while (true) {
            $start = microtime(true);
            $callback();
            $end = microtime(true);

            yield [
                'label' => $this->label,
                'microseconds' => ($end * 1E6) - ($start * 1E6)
            ];
        }
    }
}
