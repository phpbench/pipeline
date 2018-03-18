<?php

namespace PhpBench\Pipeline\Tests\Unit\Extension\Core\Stage\Output;

use PhpBench\Pipeline\Core\Stage;
use PhpBench\Pipeline\Tests\Unit\Extension\Core\CoreTestCase;

class StreamOutputTest extends CoreTestCase
{
    private $path;

    public function setUp()
    {
        $this->clearWorkspace();
        $this->path = $this->workspacePath('test.log');;
    }

    public function testWritesToStream()
    {
        $result = $this->pipeline()
            ->stage('output/stream', ['stream' => $this->path])
            ->generator(['hello'])->current();

        $contents = file_get_contents($this->path);
        $this->assertEquals('hello', trim($contents));
    }

    public function testSerializesNonScalarValues()
    {
        $result = $this->pipeline()
            ->stage('output/stream', ['stream' => $this->path])
            ->generator([ [ 'hello' ] ])->current();

        $contents = file_get_contents($this->path);
        $this->assertEquals('a:1:{i:0;s:5:"hello";}', trim($contents));
    }
}
