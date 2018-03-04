<?php

namespace PhpBench\Pipeline\Extension\Core\Stage\Output;

use PhpBench\Pipeline\Core\Stage;
use Generator;
use PhpBench\Pipeline\Core\Schema;

class StreamOutput implements Stage
{
    public function __invoke(array $config = []): Generator
    {
        $data = yield;
        $stream = fopen($config['stream'], $config['mode']);

        while (true) {
            foreach ($data as $line) {
                if (false === is_scalar($line)) {
                    $line = serialize($line);
                }

                fwrite($stream, $line);
            }

            $data = yield $data;
        }

        fclose($stream);
    }


    public function configure(Schema $schema)
    {
        $schema->setDefaults([
            'stream' => 'php://stdout',
            'mode' => 'w',
        ]);
    }
}
