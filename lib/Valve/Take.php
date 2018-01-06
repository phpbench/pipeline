<?php

namespace PhpBench\Framework\Valve;

use Generator;
use SplQueue;
use PhpBench\Framework\Step;

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

    public function generate(SplQueue $queue): Generator
    {
        $nextGenerator = $queue->dequeue()->generate($queue);

        $i = 0;
        foreach ($nextGenerator as $result) {
            yield array_merge([
                'iteration' => $i,
            ], $result);

            if (++$i === $this->quantity) {
                break;
            }
        }
    }
}
