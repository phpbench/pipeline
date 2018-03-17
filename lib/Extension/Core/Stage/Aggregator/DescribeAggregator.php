<?php

namespace PhpBench\Pipeline\Extension\Core\Stage\Aggregator;

use PhpBench\Pipeline\Core\Stage;
use Generator;
use PhpBench\Pipeline\Core\Schema;
use InvalidArgumentException;

class DescribeAggregator implements Stage
{
    public function __invoke(): Generator
    {
        list($config, $data) = yield;

        $samples = [];
        while (true) {
            $hash = $this->buildHash($data, $config);

            if (false === isset($samples[$hash])) {
                $samples[$hash] = [];
            }

            foreach ((array) $config['describe'] as $field) {
                if (false === isset($samples[$hash][$field])) {
                    $samples[$hash][$field] = [];
                }

                if (false === isset($data[$field])) {
                    throw new InvalidArgumentException(sprintf(
                        'Field "%s" does not exist in data with fields "%s"',
                        $field, implode('", "', array_keys($data))
                    ));
                }

                $samples[$hash][$field][] = $data[$field];
            }

            list($config, $data) = yield $this->describeData($samples);
        }
    }

    public function configure(Schema $schema)
    {
        $schema->setDefaults([
            'group_by' => [],
            'describe' => [],
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
