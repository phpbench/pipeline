<?php

namespace PhpBench\Pipeline\Core\Exception;

use RuntimeException;

class GeneratorMustYieldAnArray extends RuntimeException implements Exception
{
    public function __construct($data)
    {
        parent::__construct(sprintf(
            'Generator stage must yield an array, got "%s"',
            gettype($data)
        ));
    }
}
