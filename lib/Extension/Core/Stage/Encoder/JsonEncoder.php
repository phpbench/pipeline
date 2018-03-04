<?php

namespace PhpBench\Pipeline\Extension\Core\Stage\Encoder;

use PhpBench\Pipeline\Core\Stage;
use Generator;
use PhpBench\Pipeline\Core\Schema;
use PhpBench\Pipeline\Core\StageRegistry;

class JsonEncoder implements Stage
{
    public function __invoke(StageRegistry $registry, array $config = []): Generator
    {
        $data = yield;
        $flags = null;

        if ($config['pretty']) {
            $flags = JSON_PRETTY_PRINT;
        }

        while (true) {
            $data = yield [ json_encode($data, $flags) ];
        }
    }

    public function configure(Schema $schema)
    {
        $schema->setDefaults([
            'pretty' => false,
        ]);
    }
}
