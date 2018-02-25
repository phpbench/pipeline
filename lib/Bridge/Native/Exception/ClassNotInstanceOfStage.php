<?php

namespace PhpBench\Pipeline\Bridge\Native\Exception;

use RuntimeException;
use PhpBench\Pipeline\Core\Exception\Exception;

class ClassNotInstanceOfStage extends RuntimeException implements Exception
{
    public function __construct(string $class)
    {
        parent::__construct(sprintf(
            'Class "%s" is not an instance of PhpBench\Pipeline\Stage',
            $class
        ));
    }
}
