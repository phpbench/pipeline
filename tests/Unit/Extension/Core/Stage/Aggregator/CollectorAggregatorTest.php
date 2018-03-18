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

    public function testLimitsCollectionToAGivenSize()
    {
        $result = $this->pipeline()
            ->stage(function () {
                yield;
                yield ['one' => 1];
                yield ['one' => 2];
                yield ['one' => 3];
                yield ['one' => 4];
            })
            ->stage('aggregator/collector', ['limit' => 2])
            ->run();

        $this->assertCount(2, $result);
        $this->assertEquals([
            [ 'one' => 3 ],
            [ 'one' => 4 ],
        ], $result);
    }
}
