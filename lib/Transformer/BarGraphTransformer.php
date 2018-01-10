<?php

namespace PhpBench\Framework\Transformer;

use PhpBench\Framework\Step;
use Generator;
use PhpBench\Framework\Pipeline;
use InvalidArgumentException;
use PhpBench\Framework\Util\Assert;

class BarGraphTransformer implements Step
{
    const PADDING = 1;

    /**
     * @var string
     */
    private $labelField;

    /**
     * @var string
     */
    private $valueField;

    /**
     * @var int
     */
    private $maxWidth;

    public function __construct(string $labelField, string $valueField, int $maxWidth = 50)
    {
        $this->labelField = $labelField;
        $this->valueField = $valueField;
        $this->maxWidth = $maxWidth;
    }

    public function generator(Pipeline $pipeline): Generator
    {
        foreach ($pipeline->pop() as $data) {
            $data = (array) $data;

            yield $this->graph($data);
        }
    }

    private function graph(array $data)
    {
        $graph = [];
        $labelWidth = $this->maxLabelWidth($data);
        $maxValue = $this->maxValue($data);
        $barWidth = $this->barWidth($maxValue, $maxValue);

        foreach ($data as $row) {
            $graph[] = sprintf(
                '%-' . $labelWidth . 's |%-' . $barWidth . 's %s',
                $row[$this->labelField],
                $this->bar($row, $maxValue),
                $row[$this->valueField]
            );
        }

        return implode(PHP_EOL, $graph) . PHP_EOL;
    }

    private function maxLabelWidth(array $data): int
    {
        $max = 0;

        foreach ($data as $row) {
            $row = (array) $row;
            Assert::hasKey($row, $this->labelField);
            $length = mb_strlen($row[$this->labelField]);
            if ($length > $max) {
                $max = $length;
            }
        }

        return $max + self::PADDING;
    }

    private function barWidth(int $max, int $current)
    {
        if ($max == 0) {
            return $max;
        }
        return $current / $max * $this->maxWidth;
    }

    private function maxValue(array $data)
    {
        $max = 0;
        foreach ($data as $row) {
            Assert::hasKey($row, $this->valueField);
            $value = $row[$this->valueField];

            if ($value > $max) {
                $max = $value;
            }
        }

        return $max;
    }

    private function bar($row, $maxValue)
    {
        $bar = str_repeat('=', $this->barWidth($maxValue, $row[$this->valueField]));

        if (mb_strlen($bar) > 0) {
            $bar = substr($bar, 0, -1) . '|';
        }

        return $bar;
    }
}
