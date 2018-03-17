<?php

namespace PhpBench\Pipeline\Tests\Unit\Extension\Core\Stage\Filter;

use PhpBench\Pipeline\Tests\Unit\Extension\Core\CoreTestCase;

class KeysFilterTest extends CoreTestCase
{
    public function testItFiltersByArrayKeys()
    {
        $result = $this->pipeline()->stage('filter/keys', ['keys' => ['two', 'three']])
            ->generator()
            ->send([
                'one' => 1,
                'two' => 2,
                'three' => 3,
                'four' => 4,
            ]);

        $this->assertEquals([
            'two' => 2,
            'three' => 3,
        ], $result);
    }
}
