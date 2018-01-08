<?php

namespace PhpBench\Framework;

class Generator implements Step
{
    /**
     * @var string
     */
    private $charge;

    public function __construct($charge)
    {
        $this->charge = $charge;
    }

    public function generator(Pipeline $pipeline): Generator
    {
        $nextGenerator = $pipeline->shift();

        foreach ($nextGenerator as $input) {
            // ∞
        }
    }
}
