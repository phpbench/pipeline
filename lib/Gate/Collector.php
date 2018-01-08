<?php

namespace PhpBench\Framework\Gate;

use Generator;
use SplQueue;
use PhpBench\Framework\Step;

class Collector implements Step
{
    public function generate(SplQueue $queue): Generator
    {
        $nextGenerator = $queue->dequeue()->generate($queue);

        $collection = [];
        foreach ($nextGenerator as $result) {
            $collection[] = $result;
            yield $collection;
        }
    }
}
