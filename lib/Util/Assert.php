<?php

namespace PhpBench\Pipeline\Util;

use PhpBench\Pipeline\Exception\AssertionFailure;

class Assert
{
    public function hasKey(array $array, string $key, string $context = 'array')
    {
        if (false === array_key_exists($key, $array)) {
            throw new AssertionFailure(sprintf(
                '%s does not have key "%s", available keys: "%s"',
                $context, $key, implode('", "', array_keys($array))
            ));
        }
    }
}
