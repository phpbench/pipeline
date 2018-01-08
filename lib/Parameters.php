<?php

namespace PhpBench\Framework;

use PhpBench\Framework\Step;
use Generator;
use PhpBench\Framework\Pipeline;

class Parameters implements Step
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
        foreach ($pipeline->shift() as $result) {
            yield array_merge(
                $parameters,
                $result
            );
        }
    }
}
