<?php

namespace PhpBench\Pipeline\Tests\Unit\Core\Stage\Sampler;

use PhpBench\Pipeline\Tests\Unit\Core\StageTestCase;
use PhpBench\Pipeline\Core\Stage\Sampler\CallableSampler;

class CallableSamplerTest extends StageTestCase
{
    public function testProfilesClosure()
    {
        $result = $this->builder()
            ->stage(CallableSampler::class, [
                'callable' => function (array $data) {
                }
            ])
            ->build()()->send([]);

        $this->assertArrayHasKey('time', $result);
        $this->assertEquals('Callable', $result['name']);
    }

    public function testProfilesClassMethod()
    {
        $result = $this->builder()
            ->stage(CallableSampler::class, [
                'callable' => [ $this, 'stubCallable' ],
            ])
            ->build()()->send([]);

        $this->assertArrayHasKey('time', $result);
    }

    public function testIteratesCallable()
    {
        $count = 0;
        $result = $this->builder()
            ->stage(CallableSampler::class, [
                'callable' => function (array $data) use (&$count) {
                    $count++;
                },
                'iterations' => 100,
            ])
            ->build()()->send([]);

        $this->assertEquals(100, $count);
    }

    public function stubCallable()
    {
    }
}
