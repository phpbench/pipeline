<?php

namespace PhpBench\Pipeline\Extension\Core\Stage\Valve;

use PhpBench\Pipeline\Core\Stage;
use Generator;
use PhpBench\Pipeline\Core\Schema;

class DelayValve implements Stage
{
    public function __invoke(): Generator
    {
        list($config, $data) = yield;

        while (true) {
            usleep($config['time']);
            list($config, $data) = yield $data;
        }
    }

    public function configure(Schema $schema)
    {
        $schema->setDefaults([
            'time' => 10000,
        ]);
    }
}
