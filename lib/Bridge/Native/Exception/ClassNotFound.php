<?php

namespace PhpBench\Pipeline\Bridge\Native\Exception;

use PhpBench\Pipeline\Core\Exception\Exception;
use RuntimeException;

class ClassNotFound extends RuntimeException implements Exception
{
    public function __construct(string $class)
    {
        parent::__construct(sprintf(
            'Class "%s" not found',
            $class
        ));
    }
}
