<?php
use PhpBench\Pipeline\Output\StdOut;
use PhpBench\Pipeline\Pipeline;
use PhpBench\Pipeline\Sampler\CallbackSampler;
use PhpBench\Pipeline\Battery;
use PhpBench\Pipeline\Parameters\FixedParameters;
use PhpBench\Pipeline\Gate\QuantityGate;
use PhpBench\Pipeline\Transformer\JsonTransformer;
use PhpBench\Pipeline\Transformer\TableTransformer;
use PhpBench\Pipeline\Aggregation\Collector;
use PhpBench\Pipeline\Splitter\RotarySplitter;
use PhpBench\Pipeline\Aggregation\SummaryAggregator;
use PhpBench\Pipeline\Gate\Delay;
use PhpBench\Pipeline\Parameters\SerialParameter;
use PhpBench\Pipeline\Transformer\AnsiRedrawOutputTransformer;
use PhpBench\Pipeline\Transformer\BarMeterTransformer;
use PhpBench\Pipeline\Gate\Batch;
use PhpBench\Pipeline\Transformer\ConcatTransformer;

require 'vendor/autoload.php';

$pipeline = new Pipeline([
    new Battery('∞'),
    new FixedParameters([
        'string' => 'Hello World',
        'revs' => 10,
        'algo' => 'md5',
    ]),
    new SerialParameter('algo', hash_algos()),
    new RotarySplitter([
        new CallbackSampler([
            'label' => ' %revs% x %algo%',
            'callback' => function (array $params) {
                hash($params['algo'], $params['string']);
            },
            'revs' => '%revs%',
        ]),
    ]),
    new SummaryAggregator([ 'label' ], [ 'time' ]),
    new RotarySplitter([
        new BarMeterTransformer('label', 'time-mean'),
        //new TableTransformer(),
    ]),
    new Batch(1),
    new QuantityGate(1000),
    new ConcatTransformer(),
    new AnsiRedrawOutputTransformer(),
    new StdOut(),
]);

$pipeline->run();
