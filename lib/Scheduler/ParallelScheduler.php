<?php

namespace PhpBench\Framework\Scheduler;

use PhpBench\Framework\Logger;
use PhpBench\Framework\Step;
use Generator;
use SplQueue;

class ParallelScheduler implements Step
{
    /**
     * @var array
     */
    private $steps;

    public function __construct(array $steps)
    {
        $this->steps = $steps;
    }

    public function generate(SplQueue $queue): Generator
    {
        $generators = [];

        /** @var Step $step */
        foreach ($this->steps as $step) {
            $generators[] = $step->generate($queue);
        }

        while ($generators) {
            foreach ($generators as $index => $generator) {
                if (false === $generator->valid()) {
                    unset($generators[$index]);
                    continue;
                }


                if (false === $generator->valid()) {
                    unset($generators[$index]);
                    continue;
                }
                yield $generator->current();
                $generator->next();
            }
        }
    }
}
