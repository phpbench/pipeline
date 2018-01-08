<?php

namespace PhpBench\Framework;

use PhpBench\Framework\Step;
use Generator;
use PhpBench\Framework\Pipeline;

class Take implements Step
{
    /**
     * @var int
     */
    private $quantity;

    public function __construct(int $quantity)
    {
        $this->quantity = $quantity;
    }

    public function generator(Pipeline $pipeline): Generator
    {
        $count = 0;
        foreach ($pipeline->shift() as $result) {
            if ($count++ === $this->quantity) {
                break;
            }

            yield $result;
        }
    }
}
