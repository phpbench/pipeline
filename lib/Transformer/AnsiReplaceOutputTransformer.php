<?php

namespace PhpBench\Framework\Transformer;

use PhpBench\Framework\Result;
use PhpBench\Framework\Step;
use SplQueue;
use Generator;
use PhpBench\Framework\Pipeline;

class AnsiReplaceOutputTransformer implements Step
{
    const CLEAR_LINE = "\x1B[2K";
    const CURSOR_COL_ZERO = "\x1B[0G";

    public function generator(Pipeline $pipeline): Generator
    {
        $lastResult = null;
        $isFirst = true;
        foreach ($pipeline->pop() as $result) {
            if ($lastResult) {
                $result = self::CLEAR_LINE . $result;
                $result = self::CURSOR_COL_ZERO . $result;
                $result = $this->resetYPosition($lastResult, $result, $isFirst);
                $isFirst = false;
            }

            yield $result;
            $lastResult = $result;
        }
    }

    private function resetYPosition($lastResult, $result, bool $isFirst)
    {
        $lastHeight = substr_count($result, PHP_EOL);
        $isFirst ? $lastHeight-- : $lastHeight;
        return "\x1B[" . ($lastHeight) . 'A' . $result; // reset cursor Y pos
    }
}
