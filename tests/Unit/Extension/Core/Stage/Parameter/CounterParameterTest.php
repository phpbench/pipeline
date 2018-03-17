<?php

namespace PhpBench\Pipeline\Tests\Unit\Extension\Core\Stage\Parameter;

use PhpBench\Pipeline\Tests\Unit\Extension\Core\CoreTestCase;

class CounterParameterTest extends CoreTestCase
{
    public function testYieldsAnIncrementingValue()
    {
        $generator = $this->pipeline()
            ->stage('parameter/counter', [
                'name' => 'inc',
            ])
            ->generator();

        $generator->next();
        $generator->next();
        $generator->next();
        $generator->next();
        $generator->next();
        $generator->next();

        $result = $generator->current();

        $this->assertEquals(['inc' => 7], $result);
    }

    public function testYieldsAnIncrementingValueWithStep2()
    {
        $generator = $this->pipeline()
            ->stage('parameter/counter', [
                'name' => 'inc',
                'step' => 2,
            ])
            ->generator();

        $generator->next();
        $generator->next();
        $generator->next();
        $generator->next();
        $generator->next();
        $generator->next();

        $result = $generator->current();

        $this->assertEquals(['inc' => 14], $result);
    }
}
