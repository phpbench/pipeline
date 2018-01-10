<?php

namespace PhpBench\Framework\Util;

use PhpBench\Framework\Exception\AssertionFailure;

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
