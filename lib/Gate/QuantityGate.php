<?php

namespace PhpBench\Framework\Gate;

use PhpBench\Framework\Step;
use Generator;
use PhpBench\Framework\Pipeline;
use PhpBench\Framework\Util\Assert;
use PhpBench\Framework\Exception\AssertionFailure;

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
