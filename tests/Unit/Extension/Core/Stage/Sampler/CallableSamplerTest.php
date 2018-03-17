<?php

namespace PhpBench\Pipeline\Tests\Unit\Extension\Core\Stage\Sampler;

use PhpBench\Pipeline\Tests\Unit\Extension\Core\CoreTestCase;

class CallableSamplerTest extends CoreTestCase
{
    public function testProfilesClosure()
    {
        $result = $this->pipeline()
            ->stage('sampler/callable', [
                'callable' => function (array $data) {
                },
            ])
            ->generator()->send([]);

        $this->assertArrayHasKey('time', $result);
    }

    public function testProfilesClassMethod()
    {
        $result = $this->pipeline()
            ->stage('sampler/callable', [
                'callable' => [$this, 'stubCallable'],
            ])
            ->generator()->send([]);

        $this->assertArrayHasKey('time', $result);
    }

    public function testIteratesCallable()
    {
        $count = 0;
        $result = $this->pipeline()
            ->stage('sampler/callable', [
                'callable' => function (array $data) use (&$count) {
                    ++$count;
                },
                'iterations' => 100,
                ])
            ->generator()->send([]);

        $this->assertEquals(100, $count);
    }

    public function stubCallable()
    {
    }
}
