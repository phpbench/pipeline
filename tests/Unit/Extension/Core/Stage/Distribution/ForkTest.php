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
            ->stage('valve/take', ['quantity' => 1 ])
            ->stage('distribution/fork', [
                'stages' => [
                    function () use (&$forked) {
                        $data = yield;
                        $forked[] = 'Forked Data';
                    }
                ],
            ])
            ->run(['Mainline Data']);

        $this->assertEquals([
            'Mainline Data',
        ], $result, 'Main pipeline data is not affected');

        $this->assertEquals([
            'Forked Data',
        ], $forked);
    }

    public function testForksDataToMultipleStages()
    {
        $forked = [];
        $result = $this->pipeline()
            ->stage('valve/take', ['quantity' => 1 ])
            ->stage('distribution/fork', [
                'stages' => [
                    function () use (&$forked) {
                        list($config, $data) = yield;
                        $forked[] = 'Forked Data 1';
                    },
                    function () use (&$forked) {
                        list($config, $data) = yield;
                        $forked[] = 'Forked Data 2';
                    }
                ],
            ])
            ->run(['Mainline Data']);

        $this->assertEquals([
            'Mainline Data',
        ], $result, 'Main pipeline data is not affected');

        $this->assertEquals([
            'Forked Data 1',
            'Forked Data 2',
        ], $forked);
    }
}
