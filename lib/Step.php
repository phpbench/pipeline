<?php

namespace PhpBench\Framework;

use Generator;

interface Step
{
    public function generator(Pipeline $pipeline): Generator;
}
