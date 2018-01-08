<?php

namespace PhpBench\Framework;

use PhpBench\Framework\Step;
use Generator;
use PhpBench\Framework\Pipeline;

class StdOut implements Step
{
    public function generator(Pipeline $pipeline): Generator
    {
        $generator = $pipeline->pop();

        foreach ($generator as $result) {
            echo $result;
        }
    }
}
