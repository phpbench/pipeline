<?php

namespace PhpBench\Framework;

use SplQueue;

class Pipeline
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

    public function generator()
    {
        $step = $this->steps->dequeue();
        return $step->generate($this->steps);
    }

    private function add(Step $step)
    {
        $this->steps->enqueue($step);
    }
}
