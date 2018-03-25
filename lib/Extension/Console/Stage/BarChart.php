<?php

namespace PhpBench\Pipeline\Extension\Console\Stage;

use Generator;
use PhpBench\Pipeline\Core\Stage;
use PhpBench\Pipeline\Core\Schema;
use PhpBench\Pipeline\Extension\Console\Util\ConsoleUtil;
use IntlChar;
use PhpBench\Pipeline\Core\Exception\InvalidArgumentException;

/**
 * TODO: Lots of refactoring to do here.
 */
class BarChart implements Stage
{
    const PADDING = 1;

    public function __invoke(): Generator
    {
        list($config, $data) = yield;

        while (true) {
            foreach ($data as &$row) {
                $row = (array) $row;

                if (false === array_key_exists($config['label'], $row)) {
                    throw new InvalidArgumentException(sprintf(
                        'Label field "%s" doesn\'t exist, available fields: "%s"',
                        $config['label'],
                        implode('", "', array_keys($row))
                    ));
                }

                if (false === array_key_exists($config['value'], $row)) {
                    throw new InvalidArgumentException(sprintf(
                        'Value field "%s" doesn\'t exist, available fields: "%s"',
                        $config['value'],
                        implode('", "', array_keys($row))
                    ));
                }
            }

            list($config, $data) = yield [ $this->graph($data, $config['label'], $config['value'], $config['width']) ];
        }
    }

    public function configure(Schema $schema)
    {
        $schema->setRequired([
            'label',
            'value'
        ]);

        $schema->setDefaults([
            'width' => 50,
        ]);
    }

    private function graph(array $data, string $labelField, string $valueField, int $maxWidth)
    {
        $graph = [];
        $labelWidth = $this->maxLabelWidth($data, $labelField);
        $maxValue = $this->maxValue($data, $valueField);
        $barWidth = $this->barWidth($maxValue, $maxValue, $maxWidth);

        foreach ($data as $row) {
            $graph[] = sprintf(
                '%-' . $labelWidth . 's |%s %s',
                $row[$labelField],
                ConsoleUtil::pad($this->bar($row[$valueField], $maxValue, $maxWidth), $barWidth),
                $row[$valueField]
            );
        }

        return implode(PHP_EOL, $graph) . PHP_EOL;
    }

    private function maxLabelWidth(array $data, string $labelField): int
    {
        $max = 0;

        foreach ($data as $row) {
            $row = (array) $row;
            $length = mb_strlen($row[$labelField]);
            if ($length > $max) {
                $max = $length;
            }
        }

        return $max + self::PADDING;
    }

    private function barWidth(float $max, float $current, int $maxWidth)
    {
        if ($max == 0) {
            return $max;
        }

        return ceil(($current / $max) * $maxWidth);
    }

    private function maxValue(array $data, $valueField)
    {
        $max = 0;
        foreach ($data as $row) {
            $value = $row[$valueField];

            if ($value > $max) {
                $max = $value;
            }
        }

        return $max;
    }

    private function bar($value, $maxValue, int $maxWidth)
    {
        if ($maxValue == 0) {
            return '';
        }

        // fill solid section
        $char = IntlChar::chr(0x2588);
        $barWidth = $this->barWidth($maxValue, $value, $maxWidth);
        $bar = '';

        if ($barWidth == 0) {
            return $bar;
        }

        // draw solid segments excepting the last one
        if ($barWidth > 1) {
            $bar .= str_repeat($char,  $barWidth - 1);
        }

        // determine final segments char
        $stepValue = $maxValue / $maxWidth;
        $remainderValue = $value - ($stepValue * floor($value / $stepValue)) ;

        // perfect fit, full segment
        if (0 == $remainderValue) {
            return $bar . $char;
        }

        $fraction = $remainderValue / $stepValue;
        $offset = (8 - ((int) floor(8 * $fraction))) % 8;

        // 0th offset is blank
        if ($offset === 0) {
            return $bar;
        }

        $char = hexdec(2588) + $offset;
        $bar .= IntlChar::chr($char);

        return $bar;
    }
}
