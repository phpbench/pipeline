<?php

namespace PhpBench\Framework\Tests\Unit\Output;

use PHPUnit\Framework\TestCase;
use PhpBench\Framework\Step;
use Prophecy\Argument;
use PhpBench\Framework\Pipeline;
use PhpBench\Framework\Output\StdOut;
use PhpBench\Framework\Tests\StepTestCase;

class StdOutTest extends StepTestCase
{
    public function testEchoResult()
    {
        ob_start();
        $this->runStep(new StdOut(), ['results']);
        $output = ob_get_clean();

        $this->assertEquals('results', $output);
    }

    public function testEchoArrayResult()
    {
        ob_start();
        $this->runStep(new StdOut(), [[ 'results' ]]);
        $output = ob_get_clean();

        $this->assertContains(<<<'EOT'
array(1) {
  [0] =>
  string(7) "results"
}
EOT
        , $output);
    }
}
