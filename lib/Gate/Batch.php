<?php

namespace PhpBench\Pipeline\Gate;

use PhpBench\Pipeline\Step;
use Generator;
use PhpBench\Pipeline\Pipeline;

class Batch implements Step
{
    /**
     * @var int
     */
    private $size;

    public function __construct(int $size)
    {
        $this->size = $size;
    }

    public function generator(Pipeline $pipeline): Generator
    {
        $batch = [];
        $index = 0;
        foreach ($pipeline->pop() as $result) {
            $index++;
            $batch[] = $result;
            if (0 === $index % $this->size) {
                yield $batch;
                $batch = [];
            }
        }
    }
}
