<?php

namespace PhpBench\Pipeline\Tests\Unit\Extension\Core\Stage\Distribution;

use PHPUnit\Framework\TestCase;
use PhpBench\Pipeline\Tests\Unit\Extension\Core\CoreTestCase;

class ForkTest extends CoreTestCase
{
    public function testForksDataToAStage()
    {
        $forked = [];
        $result = $this->pipeline()
            ->stage('distribution/fork', [
                'stages' => [
                    function () use (&$forked) {
                        $data = yield;
                        $forked[] = $data;
                        $data[] = 'foobar';
                        yield $data;
                    }
                ],
            ])
            ->generator(['Foobar'])
            ->current();

        $this->assertEquals([
            'Foobar',
        ], $result, 'Main pipeline data is not affected');

    }
}
