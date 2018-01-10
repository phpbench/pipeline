<?php

namespace PhpBench\Pipeline\Tests\Unit\Splitter;

use PHPUnit\Framework\TestCase;
use PhpBench\Pipeline\Splitter\SerialSplitter;
use PhpBench\Pipeline\Tests\StepTestCase;

class SerialSplitterTest extends StepTestCase
{
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

        $results = $this->runStep(new SerialSplitter([
            $step1, $step2
        ]), [ 'V1', 'V2', 'V3', 'V4' ]);

        $this->assertEquals([
            'S1-V1',
            'S1-V2',
            'S1-V3',
            'S1-V4',
            'S2-V1',
            'S2-V2',
            'S2-V3',
            'S2-V4',
        ], $results);
    }
}
