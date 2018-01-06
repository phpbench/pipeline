<?php

namespace PhpBench\Framework\Logger;

use PhpBench\Framework\Logger;
use PhpBench\Framework\Result;

class StdOutLogger implements Logger
{
    public function log(array $results)
    {
        echo json_encode($results) . PHP_EOL;
    }
}
