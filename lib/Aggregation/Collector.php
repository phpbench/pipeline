<?php

namespace PhpBench\Framework\Aggregation;

use PhpBench\Framework\Step;
use Generator;
use PhpBench\Framework\Pipeline;

class Collector implements Step
{
    public function generator(Pipeline $pipeline): Generator
    {
        $collection = [];
        foreach ($pipeline->pop() as $data) {
            $collection[] = $data;
            yield $collection;
        }
    }
}
