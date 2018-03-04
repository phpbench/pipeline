<?php

namespace PhpBench\Pipeline\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use PhpBench\Pipeline\Core\Schema;
use PhpBench\Pipeline\Core\Exception\InvalidConfig;

class SchemaTest extends TestCase
{
    /**
     * @var ConfigSchema
     */
    private $schema;

    public function setUp()
    {
        $this->schema = new Schema();
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

    public function testExceptionOnUnknownDefaults()
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

    public function testExceptionOnUnknownRequired()
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
