<?php

namespace PhpBench\Pipeline\Parameters;

use PhpBench\Pipeline\Step;
use Generator;
use PhpBench\Pipeline\Pipeline;

class RangeParameter implements Step
{
    /**
     * @var number
     */
    private $start;

    /**
     * @var number
     */
    private $end;

    /**
     * @var number
     */
    private $step;

    /**
     * @var string
     */
    private $field;

    public function __construct(string $field, $start, $end, $step = 1)
    {
        $this->start = $start;
        $this->end = $end;
        $this->step = $step;
        $this->field = $field;
    }

    public function generator(Pipeline $pipeline): Generator
    {
        $value = $this->start;
        $isAscending = $this->end >= $this->start;

        foreach ($pipeline->pop() as $data) {
            $data = (array) $data;

            if ($isAscending && $value > $this->end) {
                return;
            } 

            if (false === $isAscending && $value < $this->end) {
                return;
            }

            yield array_merge(
                $data, [
                    $this->field => $value
                ]
            );

            if (false === $isAscending) {
                $value -= $this->step;
                continue;
            } 

            $value += $this->step;
        }
    }
}
