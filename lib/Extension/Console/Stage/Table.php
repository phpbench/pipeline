<?php

namespace PhpBench\Pipeline\Extension\Console\Stage;

use PhpBench\Pipeline\Core\Stage;
use Generator;
use PhpBench\Pipeline\Core\Schema;
use PhpBench\Pipeline\Extension\Console\Util\ConsoleUtil;

class Table implements Stage
{
    private const PADDING = 1;

    public function __invoke(): Generator
    {
        list($config, $data) = yield;

        while (true) {
            $table = (array) $data;

            foreach ($table as &$row) {
                $row = (array) $row;

                foreach ($row as &$value) {
                    if (is_scalar($value)) {
                        continue;
                    }

                    $value = json_encode($value);
                }
            }

            $headers = $this->headers($table);
            $separator = $this->separatorRow($headers);
            array_unshift($table, $separator);
            array_unshift($table, $headers);

            $widths = $this->widths($table);
            $table = $this->table($table, $widths);
            

            list($config, $data) = yield [ implode(PHP_EOL, $table) ];
        }
    }

    private function headers(array $table): array
    {
        foreach ($table as $row) {
            return array_keys($row);
        }

        return [];
    }

    private function widths(array $table): array
    {
        $widths = [];

        foreach ($table as $row) {
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

    private function table(array $data, array $widths): array
    {
        $table = [];
        foreach ($data as $row) {
            $tableRow = [];

            foreach (array_values($row) as $colNumber => $value) {
                $width = $widths[$colNumber];
                $tableRow[] = ConsoleUtil::pad($value, $width);
            }

            $table[] = implode('', $tableRow);
        }

        return $table;
    }

    public function configure(Schema $schema)
    {
    }
}
