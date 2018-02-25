<?php

namespace PhpBench\Pipeline\Config;

class ConfigSchema
{
    private $required = [];

    private $defaults = [];

    public function setRequired(array $fields)
    {
        $this->required = $fields;
    }

    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;
    }

    public function resolve(array $config)
    {
        $allowedKeys = array_merge(array_keys($this->defaults), $this->required);

        if ($diff = array_diff(array_keys($config), $allowedKeys)) {
            throw new InvalidConfig(sprintf(
                'Keys "%s" are not known, known keys: "%s"',
                implode('", "', array_keys($config)),
                implode('", "', $allowedKeys)
            ));
        }

        $config = array_merge($this->defaults, $config);

        if ($diff = array_diff($this->required, array_keys($config))) {
            throw new InvalidConfig(sprintf(
                'Key(s) "%s" are required',
                implode('", "', $diff)
            ));
        }

        return $config;
    }
}
