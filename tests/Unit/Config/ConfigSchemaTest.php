<?php

namespace PhpBench\Pipeline\Tests\Unit\Config;

use PHPUnit\Framework\TestCase;
use PhpBench\Pipeline\ConfigSchema;
use PhpBench\Pipeline\Exception\InvalidConfig;

class ConfigSchemaTest extends TestCase
{
    /**
     * @var ConfigSchema
     */
    private $schema;

    public function setUp()
    {
        $this->schema = new ConfigSchema();
    }

    public function testResolvesDefaults()
    {
        $this->schema->setDefaults([
            'hello' => 'goodbye',
        ]);
        $this->assertEquals([
            'hello' => 'goodbye',
        ], $this->schema->resolve([]));
    }

    public function testOverridesDefaults()
    {
        $this->schema->setDefaults([
            'hello' => 'goodbye',
        ]);
        $this->assertEquals([
            'hello' => 'adios',
        ], $this->schema->resolve([
            'hello' => 'adios',
        ]));
    }

    public function testEnforcesRequiredKeys()
    {
        $this->expectException(InvalidConfig::class);
        $this->expectExceptionMessage('Key(s) "hello", "goodbye" are required');

        $this->schema->setRequired([
            'hello', 'goodbye',
        ]);
        $this->schema->resolve([]);
    }

    public function testExceptionForUnknownDefaultOptions1()
    {
        $this->expectException(InvalidConfig::class);
        $this->expectExceptionMessage('Keys "hello", "goodbye" are not known, known keys: "barbar"');

        $this->schema->setDefaults([
            'barbar' => 'foobar',
        ]);
        $this->schema->resolve([
            'hello' => 'foobar',
            'goodbye' => 'barbar',
        ]);
    }

    public function testExceptionForUnknownDefaultOptions2()
    {
        $this->expectException(InvalidConfig::class);
        $this->expectExceptionMessage('Keys "hello", "goodbye" are not known, known keys: "barbar"');

        $this->schema->setRequired([
            'barbar',
        ]);
        $this->schema->resolve([
            'hello' => 'foobar',
            'goodbye' => 'barbar',
        ]);
    }
}
