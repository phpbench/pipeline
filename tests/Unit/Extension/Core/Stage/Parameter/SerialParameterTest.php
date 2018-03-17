<?php

namespace PhpBench\Pipeline\Tests\Unit\Extension\Core\Stage\Parameter;

use PhpBench\Pipeline\Tests\Unit\Extension\Core\CoreTestCase;

class SerialParameterTest extends CoreTestCase
{
    public function testYieldsNamedSeriesInfinitely()
    {
        $generator = $this->pipeline()
            ->stage('parameter/serial', [
                'name' => 'Hello',
                'values' => [1, 2, 3, 4],
            ])
            ->generator();

        $generator->next();
        $generator->next();
        $generator->next();
        $generator->next();
        $generator->next();
        $generator->next();

        $result = $generator->current();

        $this->assertEquals(['Hello' => 3], $result);
    }
}
