<?php

namespace PhpBench\Pipeline\Tests\Unit\Extension\Core\Stage\Aggregator;

use PhpBench\Pipeline\Tests\Unit\Extension\Core\CoreTestCase;

class DescribeAggregatorTest extends CoreTestCase
{
    public function testItDescribesColumn()
    {
        $result = $this->pipeline()
            ->stage(function () {
                yield;
                yield ['one' => 'one', 'time' => 10];
                yield ['one' => 'one', 'time' => 20];
                yield ['one' => 'one', 'time' => 30];
            })
            ->stage('aggregator/describe', [
                'group_by' => ['one'],
                'describe' => ['time'],
            ])
            ->run();

        $this->assertCount(1, $result);
        $this->assertEquals([
            'one' => [
                'time' => [
                    'count' => 3,
                    'mean' => 20,
                    'min' => 10,
                    'max' => 30,
                ],
            ],
        ], $result);
    }

    public function testItGroupsByASingleColumn()
    {
        $result = $this->pipeline()
            ->stage(function () {
                yield;
                yield ['one' => 'one', 'time' => 10];
                yield ['one' => 'two', 'time' => 20];
                yield ['one' => 'three', 'time' => 20];
                yield ['one' => 'one', 'time' => 50];
            })
            ->stage('aggregator/describe', [
                'group_by' => ['one'],
                'describe' => ['time'],
            ])
            ->run();

        $this->assertCount(3, $result);
        $this->assertArrayHasKey('one', $result);
        $this->assertArrayHasKey('two', $result);
        $this->assertArrayHasKey('three', $result);
    }

    public function testItGroupsByMultipleColumns()
    {
        $result = $this->pipeline()
            ->stage(function () {
                yield;
                yield ['one' => 'one', 'two' => 'two', 'time' => 10];
                yield ['one' => 'two', 'two' => 'one', 'time' => 20];
                yield ['one' => 'three', 'two' => 'three', 'time' => 20];
                yield ['one' => 'one', 'two' => 'two', 'time' => 50];
            })
            ->stage('aggregator/describe', [
                'group_by' => ['one', 'two'],
                'describe' => ['time'],
            ])
            ->run();

        $this->assertCount(3, $result);
        $this->assertArrayHasKey('one, two', $result);
        $this->assertArrayHasKey('two, one', $result);
        $this->assertArrayHasKey('three, three', $result);
    }
}
