<?php

namespace PhpBench\Framework\Logger;

use PhpBench\Framework\Result;
use PhpBench\Framework\Step;
use SplQueue;
use Generator;

class StdOutLogger implements Step
{
    public function generate(SplQueue $queue): Generator
    {
        $nextGenerator = $queue->dequeue()->generate($queue);

        $lastResult = null;
        foreach ($nextGenerator as $result) {
            echo $this->getOutput($result);

            yield $result;
        }
    }

    private function getOutput($result)
    {
        if (is_string($result)) { 
            return $result;
        } 

        ob_start();
        var_dump($result);
        $result = ob_get_contents();
        ob_clean();

        return $result;
    }
}
