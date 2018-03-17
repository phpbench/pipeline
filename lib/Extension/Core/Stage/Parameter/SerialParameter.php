<?php

namespace PhpBench\Pipeline\Extension\Core\Stage\Parameter;

use Generator;
use PhpBench\Pipeline\Core\Stage;
use PhpBench\Pipeline\Core\Schema;

class SerialParameter implements Stage
{
    public function __invoke(): Generator
    {
        list($config, $data) = yield;
        $values = $config['values'];

        while (true) {
            for ($i = 0; $i < count($values); ++$i) {
                $data[$config['name']] = $values[$i];
                list($config, $data) = yield $data;
            }
        }
    }

    public function configure(Schema $schema)
    {
        $schema->setDefaults(['name' => 'param']);
        $schema->setRequired(['values']);
        $schema->setTypes([
            'name' => 'string',
            'values' => 'array',
        ]);
    }
}
