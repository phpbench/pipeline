<?php

namespace PhpBench\Pipeline\Tests\Integration\Sampler;

use PHPUnit\Framework\TestCase;
use PhpBench\Pipeline\Tests\StepTestCase;
use PhpBench\Pipeline\Tests\Integration\Sampler\CurlSamplerTest;
use PhpBench\Pipeline\Sampler\CurlSampler;

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
