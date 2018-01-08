<?php

namespace PhpBench\Framework\Util;

use PhpBench\Framework\Exception\InvalidStepConfiguration;

class StepConfig
{
    public function resolve(array $defaults, array $config, string $contextClass)
    {
        if ($diff = array_diff(array_keys($config), array_keys($defaults))) {
            throw new InvalidStepConfiguration(sprintf(
                'Keys "%s" for step "%s" are not valid, valid keys: "%s"',
                implode('", "', $diff), $contextClass, implode('", "', array_keys($defaults))
            ));
        }

        return array_merge($defaults, $config);
    }
}
