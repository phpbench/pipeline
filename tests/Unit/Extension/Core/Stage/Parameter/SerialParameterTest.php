<?php

namespace PhpBench\Pipeline\Tests\Unit\Extension\Core\Stage\Parameter;

use PhpBench\Pipeline\Tests\Unit\StageTestCase;
use PhpBench\Pipeline\Extension\Core\Stage\Parameter\SerialParameter;

class SerialParameterTest extends StageTestCase
{
    public function testYieldsNamedSeriesInfinitely()
    {
        $result = $this->runStage(new SerialParameter(), [
            'name' => 'Hello',
            'values' => [ 1, 2, 3, 4 ]
        ], [], 7);

        $this->assertEquals([ 'Hello' => 3 ], $result);
    }
}
