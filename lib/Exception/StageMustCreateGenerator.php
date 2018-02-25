<?php

namespace PhpBench\Pipeline\Exception;

use RuntimeException;

class StageMustCreateGenerator extends RuntimeException implements Exception
{
    public function __construct($returnedValue)
    {
        parent::__construct(sprintf(
            'Callable stage must return a Generator, got "%s"',
            is_object($returnedValue) ? get_class($returnedValue) : gettype($returnedValue)
        ));
    }
}
