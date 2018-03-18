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
    [ 'distribution/fork', [ 'stages' => [
        [ 'pipeline', [ 'stages' => [
            [ 'filter/keys', [ 'keys' => [ 'url', 'total_time', 'connect_time', 'concurrency']]],
            [ 'aggregator/collector', ['limit' => 2] ],
            [ 'encoder/json', [ 'pretty' => true ] ],
            [ 'console/redraw' ],
            [ 'output/stream' ],
        ]]],
        [ 'pipeline', [ 'stages' => [
            [ 'encoder/json' ],
            [ 'output/stream', [ 'stream' => 'report.json' ]],
        ]]]
    ]]],
]);
$builder->run();
