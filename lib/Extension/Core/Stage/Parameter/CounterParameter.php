<?php

namespace PhpBench\Pipeline\Extension\Core\Stage\Parameter;

use PhpBench\Pipeline\Core\Stage;
use Generator;
use PhpBench\Pipeline\Core\Schema;

class CounterParameter implements Stage
{
    public function __invoke(): Generator
    {
        list($config, $data) = yield;

        $count = 0;
        while (true) {
            list($config, $data) = yield [ $config['name'] => $count += $config['step'] ];
        }
    }

    public function configure(Schema $schema)
    {
        $schema->setDefaults([
            'name' => 'count',
            'step' => 1
        ]);
    }
}
