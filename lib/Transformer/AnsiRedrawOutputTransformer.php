<?php

namespace PhpBench\Framework\Transformer;

use PhpBench\Framework\Result;
use PhpBench\Framework\Step;
use SplQueue;
use Generator;
use PhpBench\Framework\Pipeline;
use RuntimeException;
use PhpBench\Framework\Util\StringUtil;

class AnsiRedrawOutputTransformer implements Step
{
    const CLEAR_LINE = "\x1B[2K";
    const CURSOR_COL_ZERO = "\x1B[0G";

    public function generator(Pipeline $pipeline): Generator
    {
        $lastResult = null;
        $isFirst = true;
        $lineLength = 0;
        foreach ($pipeline->pop() as $data) {
            $data = (array) $data;

            foreach ($data as $result) {

                if ($lastResult) {
                    $lineLength = $this->maxLineLength($result, $lineLength);
                    $result = $this->maximizeLines($result, $lineLength);
                    $result = self::CLEAR_LINE . $result;
                    $result = self::CURSOR_COL_ZERO . $result;
                    $result = $this->resetYPosition($lastResult, $result, $isFirst);
                    $isFirst = false;
                }

                yield $result;
                $lastResult = $result;
            }
        }
    }

    private function resetYPosition($lastResult, $result, bool $isFirst)
    {
        $lastHeight = substr_count($lastResult, PHP_EOL);

        return "\x1B[" . ($lastHeight) . 'A' . $result; // reset cursor Y pos
    }

    private function maxLineLength(string $result, int $maxLineLength)
    {
        foreach (explode(PHP_EOL, $result) as $line) {
            $length = mb_strlen($line);

            if ($length > $maxLineLength) {
                $maxLineLength = $length;
            }
        }

        return $maxLineLength;
    }

    private function maximizeLines(string $result, int $maxLineLength)
    {
        $lines = explode(PHP_EOL, $result);
        foreach ($lines as &$line) {
            $line = StringUtil::pad($line, $maxLineLength);
        }

        return implode(PHP_EOL, $lines);
    }
}
