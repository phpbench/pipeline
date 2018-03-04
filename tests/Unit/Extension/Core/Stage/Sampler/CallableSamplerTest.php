<?php

namespace PhpBench\Pipeline\Tests\Unit\Extension\Core\Stage\Sampler;

use PhpBench\Pipeline\Tests\Unit\Core\StageTestCase;
use PhpBench\Pipeline\Extension\Core\Stage\Sampler\CallableSampler;

class CallableSamplerTest extends StageTestCase
{
    public function testProfilesClosure()
    {
        $result = $this->runStage(new CallableSampler(), [
            'callable' => function (array $data) {
            }
        ], []);

        $this->assertArrayHasKey('time', $result);
        $this->assertEquals('Callable', $result['name']);
    }

    public function testProfilesClassMethod()
    {
        $result = $this->runStage(new CallableSampler(), [
            'callable' => [ $this, 'stubCallable' ],
        ], []);

        $this->assertArrayHasKey('time', $result);
    }

    public function testIteratesCallable()
    {
        $count = 0;
        $result = $this->runStage(new CallableSampler(), [
            'callable' => function (array $data) use (&$count) {
                $count++;
            },
            'iterations' => 100,
        ], []);

        $this->assertEquals(100, $count);
    }

    public function stubCallable()
    {
    }
}
