<?php

namespace PhpBench\Pipeline\Tests\Unit\Extension\Core\Stage\Aggregator;

use PhpBench\Pipeline\Tests\Unit\Extension\Core\CoreTestCase;
use InvalidArgumentException;

class CollectorAggregatorTest extends CoreTestCase
{
    public function testCollectsRows()
    {
        $result = $this->pipeline()
            ->stage(function () {
                yield;
                yield ['one' => 'two', 'time' => 20];
                yield ['one' => 'three', 'time' => 20];
                yield ['one' => 'one', 'time' => 50];
            })
            ->stage('aggregator/collector')
            ->run();

        $this->assertCount(3, $result);
        $this->assertEquals([
            ['one' => 'two', 'time' => 20],
            ['one' => 'three', 'time' => 20],
            ['one' => 'one', 'time' => 50],
        ], $result);
    }
}
