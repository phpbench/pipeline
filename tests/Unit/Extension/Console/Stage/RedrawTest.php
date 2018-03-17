<?php

namespace PhpBench\Pipeline\Tests\Unit\Extension\Console\Stage;

use PhpBench\Pipeline\Tests\Unit\Extension\Console\ConsoleTestCase;

class RedrawTest extends ConsoleTestCase
{
    public function testAnsiControlCharsToOverwritePreviousOutput()
    {
        $generator = $this->pipeline()
            ->stage(function () {
                yield;
                yield ['hello'];
                yield ['goodbye'];
                yield ['ciao'];
            })
            ->stage('console/redraw')
            ->generator();
        $generator->next();
        $generator->next();
        $result = $generator->current();
        $this->assertContains('ciao', $result[0]);
    }
}
