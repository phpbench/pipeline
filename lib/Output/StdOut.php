<?php

namespace PhpBench\Framework\Output;

use PhpBench\Framework\Step;
use Generator;
use PhpBench\Framework\Pipeline;

class StdOut implements Step
{
    public function generator(Pipeline $pipeline): Generator
    {
        foreach ($pipeline->pop() as $data) {

            if (false === is_scalar($data)) {
                $data = $this->dump($data);
            }

            echo $data;
            yield $data;
        }
    }

    private function dump($data)
    {
        ob_start();
        var_dump($data);
        return ob_get_clean();
    }
}