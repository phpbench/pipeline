<?php

namespace PhpBench\Framework;

use SplQueue;
use PhpBench\Framework\Step;
use Generator;

class Pipeline implements Step
{
    /**
     * @var SplQueue
     */
    private $steps;

    public function __construct(array $steps)
    {
        $this->steps = new SplQueue();
        foreach ($steps as $i => $step) {
            $this->add($step);
        }
    }

    public function generate(SplQueue $queue): Generator
    {
        $step = $this->steps->dequeue();

        return $step->generate($this->steps);
    }

    public function run()
    {
        foreach ($this->generate($this->steps) as $result) {
            // \o/
        }
    }

    private function add(Step $step)
    {
        $this->steps->enqueue($step);
    }
}
