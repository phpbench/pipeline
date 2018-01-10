<?php

namespace PhpBench\Pipeline\Parameters;

use PhpBench\Pipeline\Step;
use Generator;
use PhpBench\Pipeline\Pipeline;

class FixedParameters implements Step
{
    /**
     * @var array
     */
    private $parameters;

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function generator(Pipeline $pipeline): Generator
    {
        foreach ($pipeline->pop() as $data) {
            yield array_merge(
                (array) $data,
                $this->parameters
            );
        }
    }
}
