<?php

namespace PhpBench\Framework\Aggregation;

use PhpBench\Framework\Step;
use Generator;
use PhpBench\Framework\Pipeline;
use MathPHP\Statistics\Descriptive;
use MathPHP\Statistics\Average;
use InvalidArgumentException;

class SummaryAggregator implements Step
{
    /**
     * @var array
     */
    private $groupBy;

    /**
     * @var array
     */
    private $summarizeFields;

    public function __construct(array $groupBy = [], array $summarizeFields = [])
    {
        $this->groupBy = $groupBy;
        $this->summarizeFields = $summarizeFields;
    }

    public function generator(Pipeline $pipeline): Generator
    {
        $collection = [];
        foreach ($pipeline->pop() as $data) {
            $collection[] = (array) $data;
            $grouped = $this->groupData($collection);
            $summarized = $this->summarizeData($grouped);

            yield $summarized;
        }
    }

    private function groupData(array $collection)
    {
        if (empty($this->groupBy)) {
            return $collection;
        }

        $grouped = [];
        foreach ($collection as $row) {
            $row = (array) $row;
            $hash = [];

            foreach ($this->groupBy as $groupBy) {
                if (false === isset($row[$groupBy])) {
                    throw new InvalidArgumentException(sprintf(
                        'Unknown column "%s", known columns: "%s"',
                        $groupBy, implode('", "', array_keys($row))
                    ));
                }

                $hash[] = $row[$groupBy];
            }

            $hash = implode(', ', $hash);

            if (!isset($grouped[$hash])) {
                $grouped[$hash] = [];
            }

            $grouped[$hash][] = $row;
        }

        return $grouped;
    }

    private function summarizeData($collection)
    {
        if (empty($this->summarizeFields)) {
            return $collection;
        }

        return array_map(function ($table) {
            $fieldValues = [];
            foreach ($this->summarizeFields as $summaryField) {
                foreach ($table as $row) {
                    if (false === isset($row[$summaryField])) {
                        throw new InvalidArgumentException(sprintf(
                            'Unknown summary field "%s", available fields "%s"',
                            $summaryField, implode('", "', array_keys($row))
                        ));
                    }

                    if (false === isset($fieldValues[$summaryField])) {
                        $fieldValues[$summaryField] = [];
                    }

                    $fieldValues[$summaryField][] = $row[$summaryField];

                    unset($row[$summaryField]);
                }
            }

            $summary = [];
            foreach ($fieldValues as $field => $values) {
                $summary = array_merge($row, $summary, [
                    $field . '-mean' => Average::mean($values),
                    $field . '-min' => min($values),
                    $field . '-max' => max($values),
                    $field . '-stdev' => Descriptive::standardDeviation($values, false),
                ]);
            }

            return $summary;
        }, $collection);
    }
}
