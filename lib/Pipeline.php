<?php

namespace PhpBench\Framework;

use Iterator;

class Pipeline
{
    /**
     * @var array
     */
    private $steps;

    public function __construct(array $steps = [])
    {
        $this->steps = $steps;
    }

    public function next()
    {
        return array_shift($this->steps);
    }
}
