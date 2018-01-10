<?php

namespace PhpBench\Pipeline\Parameters;

use PhpBench\Pipeline\Step;
use Generator;
use PhpBench\Pipeline\Pipeline;

class SerialParameter implements Step
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $values;

    public function __construct(string $name, array $values)
    {
        $this->name = $name;
        $this->values = $values;
    }

    public function generator(Pipeline $pipeline): Generator
    {
        foreach ($pipeline->pop() as $data) {
            $data = (array) $data;
            foreach ($this->values as $value) {
                yield array_merge(
                    $data,
                    [
                        $this->name => $value
                    ]
                );
            }
        }
    }
}
