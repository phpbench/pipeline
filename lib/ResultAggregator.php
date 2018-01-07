<?php

namespace PhpBench\Framework;

use PhpBench\Framework\Step;
use Generator;
use SplQueue;
use RuntimeException;
use MathPHP\Statistics\Descriptive;
use MathPHP\Statistics\Average;

class ResultAggregator implements Step
{
    /**
     * @var string
     */
    private $field;

    public function __construct(string $field)
    {
        $this->field = $field;
    }

    public function generate(SplQueue $queue): Generator
    {
        $nextGenerator = $queue->dequeue()->generate($queue);
        $first = true;
        $results = [];

        while ($nextGenerator->valid()) {
            $results[] = $nextGenerator->current();

            yield $this->describeResults($results);

            if (false === $first) {
                $nextGenerator->next();
            }

            $first = false;
        }
    }

    private function describeResults(array $results)
    {
        $values = [];
        foreach ($results as $result) {
            if (!isset($result[$this->field])) {
                throw new RuntimeException(sprintf(
                    'Expected field "%s" in result with fields "%s"',
                    $this->field, implode('", "', array_keys($result))
                ));
            }

            $values[] = $result[$this->field];
        }

        $sum = array_sum($values);

        return [
            'count' => count($values),
            'sum' => $sum,
            'min' => min($values),
            'max' => max($values),
            'stdev' => Descriptive::standardDeviation($values, false),
            'mean' => Average::mean($values),
            '50%' => Descriptive::percentile($values, 90),
            '90%' => Descriptive::percentile($values, 95),
            '99%' => Descriptive::percentile($values, 99),
        ];
    }
}
