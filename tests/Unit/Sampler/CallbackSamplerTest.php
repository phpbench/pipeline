<?php

namespace PhpBench\Framework\Tests\Unit\Sampler;

use PHPUnit\Framework\TestCase;
use PhpBench\Framework\Sampler\CallbackSampler;
use PhpBench\Framework\Tests\Unit\StepTestCase;
use PhpBench\Framework\Exception\InvalidConfiguration;
use PhpBench\Framework\Exception\AssertionFailure;

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
        $this->assertArrayHasKey('microseconds', $results);
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
