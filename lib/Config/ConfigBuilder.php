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

    public function assertPositiveInteger(string $key): self
    {
        $this->assertions[] = function (Config $config) use ($key) {
            if ($config[$key] > 0) {
                return;
            }

            throw new AssertionFailure(sprintf(
                'Failed asserting that "%s" is a positive number in "%s"'
            , $config[$key], $this->context));
        };

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

        foreach ($this->assertions as $assertion) {
            $assertion($config);
        }

        return $config;
    }
}
