<?php

namespace PhpBench\Framework;

use Generator;
use PhpBench\Framework\Pipeline;
use Closure;

class CallbackSampler implements Step
{
    /**
     * @var Closure
     */
    private $closure;

    public function __construct(Closure $closure)
    {
        $this->closure = $closure;
    }

    public function generator(Pipeline $pipeline): Generator
    {
        $generator = $pipeline->shift();
        $closure = $this->closure;

        foreach ($generator as $result) {

        }
    }
}
