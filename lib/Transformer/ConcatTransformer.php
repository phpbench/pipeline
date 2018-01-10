<?php

namespace PhpBench\Pipeline\Transformer;

use PhpBench\Pipeline\Step;
use Generator;
use PhpBench\Pipeline\Pipeline;

class ConcatTransformer implements Step
{
    public function generator(Pipeline $pipeline): Generator
    {
        foreach ($pipeline->pop() as $data) {
            $data = (array) $data;
            yield implode('', $data);
        }
    }
}
