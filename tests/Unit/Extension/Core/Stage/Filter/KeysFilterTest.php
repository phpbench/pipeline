<?php

namespace PhpBench\Pipeline\Tests\Unit\Extension\Core\Stage\Filter;

use PhpBench\Pipeline\Tests\Unit\Extension\Core\CoreTestCase;
use InvalidArgumentException;

class KeysFilterTest extends CoreTestCase
{
    public function testItFiltersByArrayKeys()
    {
        $result = $this->pipeline()->stage('filter/keys', ['keys' => ['two', 'three']])
            ->generator([
                'one' => 1,
                'two' => 2,
                'three' => 3,
                'four' => 4,
            ])
            ->current();

        $this->assertEquals([
            'two' => 2,
            'three' => 3,
        ], $result);
    }

    public function testThrowsExceptionIfKeyDoesNotExist()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Data does not contain key(s) "three", it contains keys: "one", "two"');

        $result = $this->pipeline()->stage('filter/keys', ['keys' => ['one', 'two', 'three']])
            ->generator([
                'one' => 1,
                'two' => 2,
            ])
            ->current();
    }
}
