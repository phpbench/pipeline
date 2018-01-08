<?php

namespace PhpBench\Framework\Transformer;

use PhpBench\Framework\Result;
use PhpBench\Framework\Step;
use SplQueue;
use Generator;
use PhpBench\Framework\Pipeline;

class JsonTransformer implements Step
{
    public function generator(Pipeline $pipeline): Generator
    {
        foreach ($pipeline->pop() as $data) {
            yield json_encode($data);
        }
    }
}
