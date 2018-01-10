<?php

namespace PhpBench\Pipeline;

use PhpBench\Pipeline\Step;
use Generator;
use PhpBench\Pipeline\Pipeline;

class Battery implements Step
{
    public function generator(Pipeline $pipeline): Generator
    {
        while(true) {
            yield null;
        }
    }
}
