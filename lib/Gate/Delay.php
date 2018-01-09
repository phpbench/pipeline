<?php

namespace PhpBench\Framework\Gate;

use PhpBench\Framework\Step;
use Generator;
use PhpBench\Framework\Pipeline;

class Delay implements Step
{
    /**
     * @var int
     */
    private $microseconds;

    public function __construct(int $microseconds)
    {
        $this->microseconds = $microseconds;
    }

    public function generator(Pipeline $pipeline): Generator
    {
        foreach ($pipeline->pop() as $data) {
            yield $data;
            usleep($this->microseconds);
        }
    }
}
