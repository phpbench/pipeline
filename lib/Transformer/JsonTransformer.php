<?php

namespace PhpBench\Pipeline\Transformer;

use PhpBench\Pipeline\Result;
use PhpBench\Pipeline\Step;
use SplQueue;
use Generator;
use PhpBench\Pipeline\Pipeline;

class JsonTransformer implements Step
{
    /**
     * @var bool
     */
    private $pretty;

    public function __construct(bool $pretty = false)
    {
        $this->pretty = $pretty;
    }

    public function generator(Pipeline $pipeline): Generator
    {
        foreach ($pipeline->pop() as $data) {
            $flags = null;

            if ($this->pretty) {
                $flags = JSON_PRETTY_PRINT;
            }

            yield json_encode($data, $flags) . PHP_EOL;
        }
    }
}
