<?php

namespace PhpBench\Framework\Splitter;

use Generator;
use PhpBench\Framework\Step;
use PhpBench\Framework\Pipeline;

class RotarySplitter implements Step
{
    /**
     * @var Step[]
     */
    private $outputs = [];

    public function __construct(array $outputs = [])
    {
        foreach ($outputs as $output) {
            $this->add($output);
        }
    }

    public function generator(Pipeline $pipeline): Generator
    {
        $generators = [];

        foreach ($this->outputs as $output) {
            $generators[] = $output->generator(clone $pipeline);
        }

        $isFirst = true;
        while ($generators) {
            foreach ($generators as $index => $generator) {
                $data = $generator->current();

                yield($data);
                $generator->next();

                if (false === $generator->valid()) {
                    unset($generators[$index]);
                }
            }
        }
    }

    private function add(Step $output)
    {
        $this->outputs[] = $output;
    }
}
