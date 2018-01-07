<?php

namespace PhpBench\Framework\Encoder;

use PhpBench\Framework\Result;
use PhpBench\Framework\Step;
use SplQueue;
use Generator;

class TableEncoder implements Step
{
    const PADDING = 1;

    public function generate(SplQueue $queue): Generator
    {
        $nextGenerator = $queue->dequeue()->generate($queue);

        foreach ($nextGenerator as $result) {

            $headers = $this->headers($result);
            $separator = $this->separatorRow($headers);

            array_unshift($result, $separator);
            array_unshift($result, $headers);

            $widths = $this->widths($result);

            $table = $this->table($result, $widths);

            yield implode(PHP_EOL, $table) . PHP_EOL;
        }
    }

    private function headers(array $result): array
    {
        foreach ($result as $row) {
            $headers = [];
            foreach ($row as $header => $value) {
                $headers[] = $header;
            }
            return $headers;
        }

        return [];
    }

    private function widths(array $result): array
    {
        $widths = [];

        foreach ($result as $row) {
            foreach (array_values($row) as $colNumber => $value) {
                $width = strlen($value) + self::PADDING;
                if (false === isset($widths[$colNumber]) || $widths[$colNumber] < $width) {
                    $widths[$colNumber] = $width;
                }
            }
        }

        return $widths;
    }

    private function separatorRow(array $headers): array
    {
        return array_map(function ($header) {
            return str_repeat('-', strlen($header));
        }, $headers);
    }

    private function table(array $result, array $widths): array
    {
        $table = [];
        foreach ($result as $row) {
            $tableRow = [];

            foreach (array_values($row) as $colNumber => $value) {
                $width = $widths[$colNumber];
                $tableRow[] = sprintf('%-' . $width . 's', $value);
            }

            $table[] = implode('', $tableRow);
        }

        return $table;
    }
}
