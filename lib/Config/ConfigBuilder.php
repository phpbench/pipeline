<?php

namespace PhpBench\Framework\Config;

use PhpBench\Framework\Exception\InvalidConfiguration;
use PhpBench\Framework\Util\Assert;
use PhpBench\Framework\Exception\AssertionFailure;
use PhpBench\Framework\Config\Config;

class ConfigBuilder
{
    /**
     * @var array
     */
    private $defaults;

    /**
     * @var string
     */
    private $context;

    private function __construct(string $context)
    {
        $this->context = $context;
    }

    public static function create(string $context): self
    {
        return new self($context);
    }

    public function defaults(array $defaults): self
    {
        $this->defaults = $defaults;

        return $this;
    }

    public function build(array $config): Config
    {
        $defaultKeys = array_keys($this->defaults);

        if ($diff = array_diff(array_keys($config), $defaultKeys)) {
            throw new InvalidConfiguration(sprintf(
                'Keys "%s" for step "%s" are not valid, valid keys: "%s"',
                implode('", "', $diff), $this->context, implode('", "', $defaultKeys)
            ));
        }

        $config = new Config(array_merge($this->defaults, $config));

        return $config;
    }
}