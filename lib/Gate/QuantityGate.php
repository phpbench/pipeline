<?php

namespace PhpBench\Pipeline\Gate;

use PhpBench\Pipeline\Step;
use Generator;
use PhpBench\Pipeline\Pipeline;
use PhpBench\Pipeline\Util\Assert;
use PhpBench\Pipeline\Exception\AssertionFailure;

class QuantityGate implements Step
{
    /**
     * @var int
     */
    private $quantity;

    public function __construct(int $quantity)
    {
        if ($quantity < 0) {
            throw new AssertionFailure(sprintf(
                'Quantity must be a positive integer, got %s'
            , $quantity));
        }

        $this->quantity = $quantity;
    }

    public function generator(Pipeline $pipeline): Generator
    {
        $count = 0;
        foreach ($pipeline->pop() as $data) {
            if ($count++ === $this->quantity) {
                return $data;
            }

            yield $data;
        }
    }
}
