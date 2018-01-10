<?php

namespace PhpBench\Pipeline\Tests\Unit\Sampler;

use PHPUnit\Framework\TestCase;
use PhpBench\Pipeline\Sampler\CallbackSampler;
use PhpBench\Pipeline\Tests\StepTestCase;
use PhpBench\Pipeline\Exception\InvalidConfiguration;
use PhpBench\Pipeline\Exception\AssertionFailure;

class CallbackSamplerTest extends StepTestCase
{
    public function testSample()
    {
        $called = false;
        $sampler = new CallbackSampler([
            'label' => 'Hello',
            'callback' => function () use (&$called) {
                $called = true;
            },
        ]);

        $results = $this->runStep($sampler, [ [ 'one' => 'two' ] ]);
        $results = array_shift($results);

        $this->assertTrue($called);
        $this->assertArrayHasKey('label', $results);
        $this->assertArrayHasKey('time', $results);
        $this->assertArrayHasKey('parameters', $results);
        $this->assertEquals('Hello', $results['label']);
        $this->assertEquals(['one' => 'two'], $results['parameters']);
    }

    public function testRevolutions()
    {
        $count = 0;
        $sampler = new CallbackSampler([
            'revs' => 10000,
            'label' => 'Hello',
            'callback' => function () use (&$count) {
                $count++;
            },
        ]);

        $this->runStep($sampler, [ null ]);
        $this->assertEquals(10000, $count);
    }

    public function testInvalidConfig()
    {
        $this->expectException(InvalidConfiguration::class);
        $this->expectExceptionMessage('Keys "invalid" for ');
        $sampler = new CallbackSampler([
            'invalid' => 'yeah',
        ]);
    }
}
