<?php

namespace PhpBench\Framework;

use PhpBench\Framework\Step;
use Generator;
use PhpBench\Framework\Pipeline;

class Splitter implements Step
{
    /**
     * @var array
     */
    private $steps;

    public function __construct(array $steps)
    {
        $this->steps = $steps;
    }

    public function generator(Pipeline $pipeline): Generator
    {
        foreach ($this->steps as $step) {
            $splitPipeline = $pipeline->duplicate();

            $generator = $step->generator($splitPipeline);

            foreach ($generator as $result) {
                yield $result;
            }
        }
    }

}
