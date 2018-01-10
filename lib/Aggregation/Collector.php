<?php

namespace PhpBench\Pipeline\Aggregation;

use PhpBench\Pipeline\Step;
use Generator;
use PhpBench\Pipeline\Pipeline;

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
