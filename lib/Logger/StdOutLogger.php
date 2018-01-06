<?php

namespace PhpBench\Framework\Logger;

use PhpBench\Framework\Result;
use PhpBench\Framework\Step;
use SplQueue;
use Generator;

class StdOutLogger implements Step
{
    public function generate(SplQueue $queue): Generator
    {
        $nextGenerator = $queue->dequeue()->generate($queue);

        foreach ($nextGenerator as $result) {
            echo json_encode($result) . PHP_EOL;
            yield $result;
        }
    }
}
