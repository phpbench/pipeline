<?php

namespace PhpBench\Pipeline\Extension\Core\Stage\Output;

use PhpBench\Pipeline\Core\Stage;
use Generator;
use PhpBench\Pipeline\Core\Schema;

class StreamOutput implements Stage
{
    public function __invoke(): Generator
    {
        list($config, $data) = $data = yield;

        $stream = fopen($config['stream'], $config['mode']);

        while (true) {
            foreach ($data as $line) {
                if (false === is_scalar($line)) {
                    $line = serialize($line);
                }

                fwrite($stream, $line.PHP_EOL);
            }

            list($config, $data) = yield $data;
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
