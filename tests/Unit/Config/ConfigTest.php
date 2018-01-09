<?php

namespace PhpBench\Framework\Tests\Unit\Config;

use PHPUnit\Framework\TestCase;
use PhpBench\Framework\Config\Config;
use InvalidArgumentException;

class ConfigTest extends TestCase
{
    public function testResolveNotString()
    {
        $config = new Config([ 'foo' => 1234]);
        $result = $config->resolve('foo', []);
        $this->assertEquals(1234, $result);
    }

    public function testResolveNotTokenized()
    {
        $config = new Config([ 'foo' => 'hello' ]);
        $result = $config->resolve('foo', []);
        $this->assertEquals('hello', $result);
    }

    public function testResolveTokenizedNotArrayData()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected data to be an array');
        $config = new Config([ 'foo' => '%hello%' ]);
        $config->resolve('foo', 'asd');
    }

    public function testResolveTokenizedDataKeyNotFound()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Parameter "hello" not found');
        $config = new Config([ 'foo' => '%hello%' ]);
        $config->resolve('foo', [ 'barbar' => 'asd' ]);
    }

    public function testResolveTokenizedReplace()
    {
        $config = new Config([ 'foo' => 'dsa.%hello%' ]);
        $value = $config->resolve('foo', [ 'hello' => 'asd' ]);

        $this->assertEquals('dsa.asd', $value);
    }

    public function testResolveMultipleTokenReplace()
    {
        $config = new Config([ 'foo' => '%hello% %goodbye%' ]);
        $value = $config->resolve('foo', [ 'hello' => 'asd', 'goodbye' => 'dsa' ]);

        $this->assertEquals('asd dsa', $value);
    }
}
