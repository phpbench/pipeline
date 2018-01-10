<?php

namespace PhpBench\Framework\Tests\Integration\Sampler;

use PHPUnit\Framework\TestCase;
use PhpBench\Framework\Tests\StepTestCase;
use PhpBench\Framework\Tests\Integration\Sampler\CurlSamplerTest;
use PhpBench\Framework\Sampler\CurlSampler;

class CurlSamplerTest extends StepTestCase
{
    public function testCurlSampler()
    {
        $results = $this->runStep(new CurlSampler([
            'url' => 'http://localhost'
        ]), [ null ]);
        $result = array_shift($results);
        $this->assertArrayHasKey('http_code', $result);
    }
}
