<?php

namespace PhpBench\Pipeline\Extension\Core\Stage\Aggregator;

use PhpBench\Pipeline\Core\Stage;
use Generator;
use PhpBench\Pipeline\Core\Schema;
use InvalidArgumentException;

class CollectorAggregator implements Stage
{
    public function __invoke(): Generator
    {
        list($config, $data) = yield;

        $rows = [];
        while (true) {
            if (count($rows) == $config['limit']) {
                array_shift($rows);
            }
            $rows[] = $data;

            list($config, $data) = yield $rows;
        }
    }

    public function configure(Schema $schema)
    {
        $schema->setDefaults([
            'limit' => INF,
        ]);
    }

    private function buildHash(array $row, array $config): string
    {
        $hash = [];
        foreach ((array) $config['group_by'] as $groupBy) {
            if (!isset($row[$groupBy])) {
                throw new InvalidArgumentException(sprintf(
                    'Group by field "%s" does not exist in input with fields "%s"',
                    $groupBy, implode('", "', array_keys($row))
                ));
            }
            $hash[] = $row[$groupBy];
        }

        return implode(', ', $hash);
    }

    private function describeData(array $samples)
    {
        $description = [];
        foreach ($samples as $hash => $rows) {
            foreach ($rows as $field => $values) {
                $sum = array_sum($values);
                $count = count($values);
                $description[$hash][$field] = [
                    'count' => $count,
                    'mean' => $sum / $count,
                    'min' => min($values),
                    'max' => max($values),
                ];
            }
        }

        return $description;
    }
}
