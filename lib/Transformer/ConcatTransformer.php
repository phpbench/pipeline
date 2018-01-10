<?php

namespace PhpBench\Framework\Transformer;

use PhpBench\Framework\Step;
use Generator;
use PhpBench\Framework\Pipeline;

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
