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

    /**
     * @var int
     */
    private $revs;

    public function __construct(string $label, Closure $closure, int $revs)
    {
        $this->closure = $closure;
        $this->label = $label;
        $this->revs = $revs;
    }

    public function generate(SplQueue $queue): Generator
    {
        $callback = $this->closure;

        while (true) {
            $start = microtime(true);
            for ($i = 0; $i <= $this->revs; $i++) {
                $callback();
            }
            $end = microtime(true);

            yield [
                'label' => $this->label,
                'microseconds' => (($end * 1E6) - ($start * 1E6)) / $this->revs
            ];
        }
    }
}
