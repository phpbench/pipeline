<?php

namespace PhpBench\Framework;

use SplQueue;
use PhpBench\Framework\Step;
use Generator;

class Circuit implements Step
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
        if ($this->steps !== $queue) {
            foreach ($this->steps as $step) {
                $queue->unshift($step);
            }
        }

        $nextStep = $queue->dequeue();

        return $nextStep->generate($queue);
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
