<?php

namespace PhpBench\Pipeline\Config;

use ArrayAccess;
use BadMethodCallException;
use InvalidArgumentException;
use PhpBench\Pipeline\Exception\AssertionFailure;
use PhpBench\Pipeline\Util\Assert;

class Config implements ArrayAccess
{
    /**
     * @var array
     */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->config[$offset]);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        if (isset($this->config[$offset])) {
            return $this->config[$offset];
        }

        throw new InvalidArgumentException(sprintf(
            'Unknown configuration key "%s"',
            $offset
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
        throw new BadMethodCallException(
            'Configuration is immutable'
        );
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
        throw new BadMethodCallException(
            'Configuration is immutable'
        );
    }

    public function resolve(string $key, $data)
    {
        $value = $this[$key];

        if (false === is_string($value)) {
            return $value;
        }

        if (0 === preg_match_all('{%(.*?)%}', $value, $matches)) {
            return $value;
        }

        if (!is_array($data)) {
            throw new AssertionFailure(sprintf(
                'Expected data to be an array when resolving parameter "%s", got "%s"',
                $value, gettype($data)
            ));
        }

        $keys = $matches[0];

        return strtr($value, array_combine($keys, array_map(function ($key) use ($data) {
            $key = trim($key, '%');

            Assert::hasKey($data, $key);

            return $data[$key];
        }, $keys)));
    }
}
