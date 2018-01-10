<?php

namespace PhpBench\Pipeline\Aggregation;

use PhpBench\Pipeline\Step;
use Generator;
use PhpBench\Pipeline\Pipeline;
use MathPHP\Statistics\Descriptive;
use MathPHP\Statistics\Average;
use PhpBench\Pipeline\Util\Assert;

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
                Assert::hasKey($row, $groupBy);

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
            $row = [];
            foreach ($this->summarizeFields as $summaryField) {
                foreach ($table as $row) {
                    Assert::hasKey($row, $summaryField);

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
