<?php

namespace PhpBench\Pipeline;

use Generator;

class Battery implements Step
{
    public function generator(Pipeline $pipeline): Generator
    {
        while (true) {
            yield null;
        }
    }
}
