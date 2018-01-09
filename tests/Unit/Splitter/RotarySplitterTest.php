<?php

namespace PhpBench\Framework\Tests\Unit\Splitter;

use PhpBench\Framework\Tests\Unit\StepTestCase;
use PhpBench\Framework\Step;
use PhpBench\Framework\Splitter\RotarySplitter;
use PhpBench\Framework\Pipeline;
use Prophecy\Argument;

class RotarySplitterTest extends StepTestCase
{
    public function testEmptyEmpty()
    {
        $results = $this->runStep(new RotarySplitter([]), []);
        $this->assertEquals([], $results);
    }

    public function testSingleAndEmpty()
    {
        $step1 = $this->createCallbackStep(function ($pipeline) {
            yield 'hello!';
        });

        $results = $this->runStep(new RotarySplitter([ $step1 ]), []);
        $this->assertEquals([ 'hello!' ], $results);
    }

    public function testSingleAndOneElement()
    {
        $step1 = $this->createCallbackStep(function ($pipeline) {
            yield 'hello!';
        });

        $results = $this->runStep(new RotarySplitter([
            $step1,
        ]), [ 'foobar' ]);
        $this->assertEquals([ 'hello!' ], $results);
    }

    public function testMultiple()
    {
        $step1 = $this->createCallbackStep(function ($pipeline) {
            foreach ($pipeline->pop() as $data) {
                yield 'S1-' . $data;
            }
        });

        $step2 = $this->createCallbackStep(function ($pipeline) {
            foreach ($pipeline->pop() as $data) {
                yield 'S2-' . $data;
            }
        });

        $results = $this->runStep(new RotarySplitter([
            $step1, $step2
        ]), [ 'V1', 'V2', 'V3', 'V4' ]);

        $this->assertEquals([
            'S1-V1',
            'S2-V1',
            'S1-V2',
            'S2-V2',
            'S1-V3',
            'S2-V3',
            'S1-V4',
            'S2-V4'
        ], $results);
    }
}
