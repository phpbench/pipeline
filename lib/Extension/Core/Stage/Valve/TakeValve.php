<?php

namespace PhpBench\Pipeline\Extension\Core\Stage\Valve;

use PhpBench\Pipeline\Core\Stage;
use Generator;
use PhpBench\Pipeline\Core\Schema;

class TakeValve implements Stage
{
    public function __invoke(): Generator
    {
        list($config, $data) = yield;

        for ($i = 0; $i < $config['quantity']; ++$i) {
            list($config, $data) = yield $data;
        }
    }

    public function configure(Schema $schema)
    {
        $schema->setDefaults([
            'quantity' => 1,
        ]);
    }
}
