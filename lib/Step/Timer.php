<?php

namespace PhpBench\Framework\Step;

use PhpBench\Framework\Step;
use Generator;
use SplQueue;

class Timer implements Step
{
    /**
     * @var int
     */
    private $microseconds;

    public function __construct(int $microseconds)
    {
        $this->microseconds = $microseconds;
    }

    public function generate(SplQueue $queue): Generator
    {
        $nextGenerator = $queue->dequeue()->generate($queue);

        $start = microtime(true) * 1E6;
        $end = $start + $this->microseconds;
        foreach ($nextGenerator as $result) {
            if (microtime(true) * 1E6 >= $end) {
                break;
            }
            yield $result;
        }
    }
}
