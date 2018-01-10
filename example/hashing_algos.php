<?php
use PhpBench\Framework\Output\StdOut;
use PhpBench\Framework\Pipeline;
use PhpBench\Framework\Sampler\CallbackSampler;
use PhpBench\Framework\Battery;
use PhpBench\Framework\Parameters\FixedParameters;
use PhpBench\Framework\Gate\QuantityGate;
use PhpBench\Framework\Transformer\JsonTransformer;
use PhpBench\Framework\Transformer\TableTransformer;
use PhpBench\Framework\Aggregation\Collector;
use PhpBench\Framework\Splitter\RotarySplitter;
use PhpBench\Framework\Aggregation\SummaryAggregator;
use PhpBench\Framework\Gate\Delay;
use PhpBench\Framework\Parameters\SerialParameter;
use PhpBench\Framework\Transformer\AnsiRedrawOutputTransformer;
use PhpBench\Framework\Transformer\BarGraphTransformer;
use PhpBench\Framework\Gate\Batch;
use PhpBench\Framework\Transformer\ConcatTransformer;

require 'vendor/autoload.php';

$pipeline = new Pipeline([
    new Battery('∞'),
    new FixedParameters([
        'string' => 'Hello World',
        'revs' => 1000,
        'algo' => 'md5',
    ]),
    new SerialParameter('algo', [ 'md5', 'sha1', 'sha256', 'haval160,4' ]),
    new RotarySplitter([
        new CallbackSampler([
            'label' => '%string% %algo% %revs%x',
            'callback' => function (array $params) {
                hash($params['algo'], $params['string']);
            },
            'revs' => '%revs%',
        ]),
    ]),
    new Delay(10000),
    new QuantityGate(1000),
    new SummaryAggregator([ 'label' ], [ 'µs' ]),
    //new Collector(),
    //new JsonTransformer(),
    new RotarySplitter([
        new BarGraphTransformer('label', 'µs-mean'),
        new TableTransformer(),
    ]),
    new Batch(2),
    new ConcatTransformer(),
    new AnsiRedrawOutputTransformer(),
    new StdOut(),
]);

$pipeline->run();
