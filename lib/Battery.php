<?php

namespace PhpBench\Framework;

use PhpBench\Framework\Step;
use Generator;
use PhpBench\Framework\Pipeline;

class Battery implements Step
{
    public function generator(Pipeline $pipeline): Generator
    {
        while(true) {
            yield null;
        }
    }
}
