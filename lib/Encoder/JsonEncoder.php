<?php

namespace PhpBench\Framework\Encoder;

use PhpBench\Framework\Result;
use PhpBench\Framework\Step;
use SplQueue;
use Generator;

class JsonEncoder implements Step
{
    public function generate(SplQueue $queue): Generator
    {
        $nextGenerator = $queue->dequeue()->generate($queue);

        foreach ($nextGenerator as $result) {
            yield json_encode($result);
        }
    }
}

