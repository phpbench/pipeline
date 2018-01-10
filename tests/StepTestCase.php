<?php

namespace PhpBench\Framework\Tests;

use PHPUnit\Framework\TestCase;
use PhpBench\Framework\Step;
use PhpBench\Framework\Pipeline;
use Generator;
use Closure;

class StepTestCase extends TestCase
{
    protected function runStep(Step $step, array $previousData)
    {
        $pipeline = $this->preparePipeline($previousData);
        $generator = $step->generator($pipeline);
        $results = [];

        foreach ($generator as $result) {
            $results[] = $result;
        }

        return $results;
    }

    protected function preparePipeline(array $data)
    {
        $pipeline = new Pipeline([

            $this->createCallbackStep(function () use ($data) {
                foreach ($data as $result) {
                    yield $result;
                }
            })
        ]);

        return $pipeline;
    }

    protected function createCallbackStep(Closure $closure)
    {
        return new class($closure) implements Step {

            private $closure;

            public function __construct($closure) 
            {
                $this->closure = $closure;
            }

            public function generator(Pipeline $pipeline): Generator {
                $closure = $this->closure;
                return $closure($pipeline);
            }
        };
    }
}
