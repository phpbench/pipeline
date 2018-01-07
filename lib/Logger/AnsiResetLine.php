<?php

namespace PhpBench\Framework\Logger;

use PhpBench\Framework\Result;
use PhpBench\Framework\Step;
use SplQueue;
use Generator;

class AnsiResetLine implements Step
{
    const CLEAR_LINE = "\x1B[2K";
    const CURSOR_COL_ZERO = "\x1B[0G";

    public function generate(SplQueue $queue): Generator
    {
        $nextGenerator = $queue->dequeue()->generate($queue);

        $lastResult = null;
        foreach ($nextGenerator as $result) {
            if ($lastResult) {
                $result = self::CLEAR_LINE . $result;
                $result = self::CURSOR_COL_ZERO . $result;
                $lastHeight = substr_count($result, PHP_EOL);

                $result = "\x1B[" . ($lastHeight - 1) . 'A' . $result; // reset cursor Y pos
            }

            yield $result;
            $lastResult = $result;
        }
    }
}
