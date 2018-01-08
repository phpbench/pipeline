<?php

namespace PhpBench\Framework\Util;

class Assert
{
    public static function assertGreaterThan(int $expected, int $value, string $context)
    {
        if (!$value > $expected) {
            return;
        }

        throw new AssertionFailure(sprintf(
            'Failed asserting that "%s" is greater than "%s" %s'
        ), $value, $expected, $context);
    }
}
