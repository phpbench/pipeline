<?php

require __DIR__ . '/../vendor/autoload.php';

use PhpBench\Pipeline\Core\PipelineBuilder;

$builder = PipelineBuilder::createWithDefaults()
    ->stage('parameter/serial', [
        'name' => 'algo',
        'values' => array_slice(hash_algos(), 0, 3),
    ])
    ->stage('sampler/callable', [
        'callable' => function ($data) { hash($data['algo'], 'Hello World'); },
        'iterations' => 100,
    ])
    ->stage('aggregator/describe', [ 'group_by' => 'algo', 'describe' => 'time' ])
    ->stage('encoder/json', [ 'pretty' => true ])
    ->stage('console/redraw')
    ->stage('output/stream')

    ->run();
