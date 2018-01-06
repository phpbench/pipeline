<?php

namespace PhpBench\Framework;

use Generator;
use SplQueue;

interface Step
{
    public function generate(SplQueue $queue): Generator;
}
