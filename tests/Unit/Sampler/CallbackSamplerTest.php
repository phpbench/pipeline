<?php

namespace PhpBench\Framework\Tests\Unit\Sampler;

use PHPUnit\Framework\TestCase;
use PhpBench\Framework\Sampler\CallbackSampler;
use PhpBench\Framework\Tests\Unit\StepTestCase;
use PhpBench\Framework\Exception\InvalidStepConfiguration;

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

        $this->assertTrue($called);
        $this->assertArrayHasKey('label', $results);
        $this->assertArrayHasKey('microseconds', $results);
        $this->assertArrayHasKey('parameters', $results);
        $this->assertEquals('Hello', $results['label']);
        $this->assertEquals(['one' => 'two'], $results['parameters']);
    }

    public function testInvalidConfig()
    {
        $this->expectException(InvalidStepConfiguration::class);
        $this->expectExceptionMessage('Keys "invalid" for ');
        $sampler = new CallbackSampler([
            'invalid' => 'yeah',
        ]);
    }
}
