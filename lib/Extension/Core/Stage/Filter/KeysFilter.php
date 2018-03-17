<?php

namespace PhpBench\Pipeline\Extension\Core\Stage\Filter;

use PhpBench\Pipeline\Core\Stage;
use Generator;
use PhpBench\Pipeline\Core\Schema;

class KeysFilter implements Stage
{
    public function __invoke(array $config): Generator
    {
        $data = yield;

        while (true) {
            $data = yield array_filter($data, function ($key) use ($config) {
                return in_array($key, $config['keys']);
            }, ARRAY_FILTER_USE_KEY);
        }
    }

    public function configure(Schema $schema)
    {
        $schema->setRequired(['keys']);
    }
}
