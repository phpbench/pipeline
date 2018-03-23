<?php

require __DIR__ . '/../vendor/autoload.php';

use PhpBench\Pipeline\Core\PipelineBuilder;

$builder = PipelineBuilder::createWithDefaults();
$builder->load([
    [ 'parameter/serial', [ 'name' => 'url', 'values' => [
        'https://localhost',
        'https://www.bbc.co.uk',
    ]]],
    [ 'valve/delay', [ 'time' => 10 ] ],
    [ 'sampler/curl', [ 'url' => '%url%', 'concurrency' => 10, 'async' => false ] ],
    [ 'valve/timeout', [ 'time' => 10E6 ]],
    [ 'pipeline', [
        'stages' => [
            [
                'pipeline',
                ['stages' => [
                    ['filter/keys', ['keys' => ['url', 'total_time', 'connect_time', 'concurrency']]],
                    [ 'aggregator/collector', ['limit' => 2] ],
                    //[ 'aggregator/describe', [ 'group_by' => 'url', 'describe' => 'total_time' ] ],
                    [ 'encoder/json', [ 'pretty' => true ] ],
                    'console/redraw',
                    [ 'output/stream' ]
                ]]
            ],
            [
                'pipeline',
                [ 'stages' => [
                    [ 'encoder/json' ],
                    [ 'output/stream', [ 'stream' => 'report.json', 'mode' => 'a' ]]
                ]]
            ]
        ]
    ]],
]);
$builder->run();
