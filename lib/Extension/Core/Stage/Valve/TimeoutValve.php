<?php

namespace PhpBench\Pipeline\Extension\Core\Stage\Valve;

use PhpBench\Pipeline\Core\Stage;
use Generator;
use PhpBench\Pipeline\Core\Schema;

class TimeoutValve implements Stage
{
    public function __invoke(): Generator
    {
        list($config, $data) = yield;

        $runningTime = 0;
        $start = microtime(true);
        while ($runningTime < $config['time']) {
            list($config, $data) = yield $data;
            $runningTime = (microtime(true) - $start) * 1E6;
        }
    }

    public function configure(Schema $schema)
    {
        $schema->setDefaults([
            'time' => 1E6,
        ]);
    }
}
