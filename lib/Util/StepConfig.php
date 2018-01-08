<?php

namespace PhpBench\Framework\Util;

use PhpBench\Framework\Exception\InvalidConfiguration;

class StepConfig
{
    public static function resolve(array $defaults, array $config, string $contextClass)
    {
        if ($diff = array_diff(array_keys($config), array_keys($defaults))) {
            throw new InvalidConfiguration(sprintf(
                'Keys "%s" for step "%s" are not valid, valid keys: "%s"',
                implode('", "', $diff), $contextClass, implode('", "', array_keys($defaults))
            ));
        }

        return array_merge($defaults, $config);
    }
}
