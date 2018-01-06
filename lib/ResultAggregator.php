<?php

namespace PhpBench\Framework;

use PhpBench\Framework\Step;
use Generator;
use SplQueue;
use RuntimeException;

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

            yield $this->aggregateResults($results);

            if (false === $first) {
                $nextGenerator->next();
            }

            $first = false;
        }
    }

    private function aggregateResults(array $results)
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
            'mean' => $sum / count($values),
            'min' => min($values),
            'max' => max($values),
        ];
    }
}
