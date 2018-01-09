<?php

namespace PhpBench\Framework;

use PhpBench\Framework\Exception\EmptyPipeline;
use Countable;
use Generator;

class Pipeline implements Countable
{
    /**
     * @var array
     */
    private $steps;

    public function __construct(array $steps)
    {
        $this->steps = $steps;
    }

    public function pop(): Generator
    {
        $step = array_pop($this->steps);

        if (null === $step) {
            throw new EmptyPipeline(
                'Pipeline is empty, cannot pop anything from it'
            );
        }

        return $step->generator($this);
    }

    public function count()
    {
        return count($this->steps);
    }
}
