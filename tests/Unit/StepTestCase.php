<?php

namespace PhpBench\Framework\Tests\Unit;

use PHPUnit\Framework\TestCase;
use PhpBench\Framework\Step;
use PhpBench\Framework\Pipeline;

class StepTestCase extends TestCase
{
    protected function runStep(Step $step, array $previousData)
    {
        $pipeline = $this->preparePipeline($previousData);
        $generator = $step->generator($pipeline);
        $results = [];

        foreach ($generator as $result) {
            $results[] = $result;
        }

        return $result;
    }

    protected function preparePipeline(array $data)
    {
        $pipeline = $this->prophesize(Pipeline::class);
        $pipeline->pop()->willReturn($this->generator($data));

        return $pipeline->reveal();
    }

    private function generator(array $data)
    {
        foreach ($data as $item) {
            yield $item;
        }
    }
}
