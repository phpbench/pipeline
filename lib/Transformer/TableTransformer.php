<?php

namespace PhpBench\Pipeline\Transformer;

use PhpBench\Pipeline\Step;
use Generator;
use PhpBench\Pipeline\Pipeline;
use PhpBench\Pipeline\Util\StringUtil;

class TableTransformer implements Step
{
    const PADDING = 1;

    public function generator(Pipeline $pipeline): Generator
    {
        foreach ($pipeline->pop() as $result) {
            $result = (array) $result;

            if (empty($result)) {
                yield '';
                return;
            }

            foreach ($result as &$row) {
                $row = (array) $row;
                foreach ($row as &$value) {
                    if (is_scalar($value)) {
                        continue;
                    }

                    $value = json_encode($value);
                }
            }

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
                $width = mb_strlen($value) + self::PADDING;
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
            return str_repeat('-', iconv_strlen($header));
        }, $headers);
    }

    private function table(array $result, array $widths): array
    {
        $table = [];
        foreach ($result as $row) {
            $tableRow = [];

            foreach (array_values($row) as $colNumber => $value) {
                $width = $widths[$colNumber];
                $tableRow[] = StringUtil::pad($value, $width);
            }

            $table[] = implode('', $tableRow);
        }

        return $table;
    }
}
