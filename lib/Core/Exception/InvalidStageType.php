<?php

namespace PhpBench\Pipeline\Core\Exception;

use RuntimeException;

class InvalidStageType extends RuntimeException implements Exception
{
    public function __construct($stage)
    {
        parent::__construct(sprintf(
            'Stage must be either an instanceof Stage or a callable, got "%s"',
            gettype($stage)
        ));
    }
}
