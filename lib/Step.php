<?php

namespace PhpBench\Pipeline;

use Generator;

interface Step
{
    public function generator(Pipeline $pipeline): Generator;
}
