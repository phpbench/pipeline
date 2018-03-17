<?php

namespace PhpBench\Pipeline\Tests\Unit\Extension\Core\Stage\Sampler;

use PhpBench\Pipeline\Tests\Unit\Extension\Core\CoreTestCase;
use Symfony\Component\Process\Process;

class CurlSamplerTest extends CoreTestCase
{
    const SAMPLE_URL = '127.0.0.1:8099';

    /**
     * @var Process
     */
    private $process;
    private $accessLogPath;

    public function setUp()
    {
        $this->accessLogPath = __DIR__.'/../../../../../Serve/access.log';

        if (file_exists($this->accessLogPath)) {
            unlink($this->accessLogPath);
        }

        $this->process = new Process('php -S '.self::SAMPLE_URL);
        $this->process->setWorkingDirectory(__DIR__.'/../../../../../Serve');
        $this->process->start();
        usleep(50000);
    }

    public function tearDown()
    {
        $this->process->stop();
    }

    public function testSamplesAGet()
    {
        $result = $this->pipeline()
            ->stage('sampler/curl', ['url' => self::SAMPLE_URL])
            ->generator()
            ->current();

        $this->assertArrayHasKey('url', $result);
        $this->assertArrayHasKey('content_type', $result);
        $this->assertArrayHasKey('header_size', $result);

        $request = $this->requests();
        $request = reset($request);
        $this->assertEquals('GET', $request['REQUEST_METHOD']);
    }

    public function testSamplesAPost()
    {
        $result = $this->pipeline()
            ->stage('sampler/curl', ['url' => self::SAMPLE_URL, 'method' => 'POST'])
            ->generator()
            ->current();

        $request = $this->requests();
        $request = reset($request);
        $this->assertEquals('POST', $request['REQUEST_METHOD']);
    }

    public function testSendsHeaders()
    {
        $result = $this->pipeline()
            ->stage('sampler/curl', [
                'url' => self::SAMPLE_URL,
                'headers' => [
                    'X-Header1' => 'Yes',
                    'X-Header2' => 'No',
                ], ])
            ->generator()
            ->current();

        $request = $this->requests();
        $request = reset($request);
        $this->assertEquals('Yes', $request['HTTP_X_HEADER1']);
        $this->assertEquals('No', $request['HTTP_X_HEADER2']);
    }

    public function testConcurrentRequests()
    {
        $result = $this->pipeline()
            ->stage('valve/take', [ 'quantity' => 4 ])
            ->stage('sampler/curl', [
                'url' => self::SAMPLE_URL,
                'concurrency' => 4,
                'headers' => [
                    'X-Header1' => 'Yes',
                    'X-Header2' => 'No',
                ], ])
            ->run();

        $requests = $this->requests();
        $this->assertCount(4, $requests);
    }

    private function requests(): array
    {
        $requests = [];
        foreach (explode(PHP_EOL, file_get_contents($this->accessLogPath)) as $request) {
            $requests[] = json_decode($request, true);
        }

        return array_filter($requests);
    }
}
