<?php

namespace PhpBench\Framework\Splitter;

use PhpBench\Framework\Step;
use SplQueue;
use Generator;
use DeepCopy\DeepCopy;

class Splitter implements Step
{
    /**
     * @var array
     */
    private $steps;

    /**
     * @var DeepCopy
     */
    private $copier;

    public function __construct(array $steps)
    {
        $this->steps = $steps;
        $this->copier = new DeepCopy();
    }

    public function generate(SplQueue $queue): Generator
    {
        foreach ($this->steps as $step) {
            $stepQueue = $this->copier->copy($queue);

            foreach ($step->generate($stepQueue) as $result) {
                yield $result;
            }
        }
    }
}
