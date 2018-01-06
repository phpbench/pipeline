<?php

namespace PhpBench\Framework\Scheduler;

use PhpBench\Framework\Logger;

class ParallelScheduler
{
    /**
     * @var array
     */
    private $pipelines;

    public function __construct(array $pipelines)
    {
        $this->pipelines = $pipelines;
    }

    public function run(Logger $logger)
    {
        $generators = [];

        foreach ($this->pipelines as $pipeline) {
            $generators[] = $pipeline->generator();
        }

        while ($generators) {
            foreach ($generators as $index => $generator) {
                if (false === $generator->valid()) {
                    unset($generators[$index]);
                    continue;
                }
                $logger->log($generator->current());
                $generator->next();
            }
        }
    }
}
