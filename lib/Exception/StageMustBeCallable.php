<?php

namespace PhpBench\Pipeline\Exception;

use InvalidArgumentException;

class StageMustBeCallable extends InvalidArgumentException implements Exception
{
    public function __construct($stage)
    {
        parent::__construct(sprintf(
            'Stage must be a callable (e.g. closure, invokable class, or other callback), got "%s"',
            gettype($stage)
        ));
    }
}
