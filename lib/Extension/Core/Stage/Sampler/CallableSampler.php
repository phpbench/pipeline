<?php

namespace PhpBench\Pipeline\Extension\Core\Stage\Sampler;

use PhpBench\Pipeline\Core\Stage;
use Generator;
use PhpBench\Pipeline\Core\Schema;

class CallableSampler implements Stage
{
    public function __invoke(array $config = []): Generator
    {
        $data = yield;
        $nbIterations = $config['iterations'];
        $callable = $config['callable'];

        while (true) {
            $start = microtime(true);

            for ($i = 0; $i < $nbIterations; $i++) {
                call_user_func($callable, $data);
            }

            $end = microtime(true);
            $time = (($end * 1E6) - ($start * 1E6)) / $nbIterations;
            $data['time'] = $time;
            $data['name'] = $config['name'];
            $data = yield $data;
        }
    }

    public function configure(Schema $schema)
    {
        $schema->setDefaults([
            'iterations' => 1,
            'name' => 'Callable',
        ]);
        $schema->setRequired([
            'callable'
        ]);
    }
}
