<?php

namespace PhpBench\Framework\Tests\Unit\Aggregation;

use PhpBench\Framework\Tests\Unit\StepTestCase;
use PhpBench\Framework\Aggregation\SummaryAggregator;

class StatisticalAggregatorTest extends StepTestCase
{
    public function testEmpty()
    {
        $results = $this->runStep(new SummaryAggregator(), []);
        $this->assertEquals([  ], $results);
    }

    public function testScalar()
    {
        $results = $this->runStep(new SummaryAggregator(), [ 'hello' ]);
        $this->assertEquals([ [ [ 'hello' ] ] ], $results);
    }

    public function testRows()
    {
        $results = $this->runStep(new SummaryAggregator(), [ [ 'hello', 1234 ] ]);
        $this->assertEquals([ [ [ 'hello', 1234 ] ] ], $results);
    }

    public function testGroupBy()
    {
        $results = $this->runStep(new SummaryAggregator([0]), [
            [ 'hello', 1234 ],
            [ 'hello', 1234 ],
            [ 'goodbye', 1234 ],
        ]);
        $finalResult = array_pop($results);
        $this->assertEquals([
            'hello' =>  [ 
                [ 'hello', 1234 ],
                [ 'hello', 1234 ],
            ],
            'goodbye' => [
                [ 'goodbye' , 1234 ]
            ],
        ] , $finalResult);
    }

    public function testSummarize()
    {
        $results = $this->runStep(new SummaryAggregator([ 'label' ], [ 'seconds' ]), [
            [ 'label' => 'hello', 'seconds' => 2 ],
            [ 'label' => 'hello', 'seconds' => 4 ],
            [ 'label' => 'goodbye', 'seconds' => 6 ],
        ]);
        $finalResult = array_pop($results);
    }
}
