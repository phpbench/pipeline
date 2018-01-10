<?php
use PhpBench\Pipeline\Output\StdOut;
use PhpBench\Pipeline\Pipeline;
use PhpBench\Pipeline\Battery;
use PhpBench\Pipeline\Transformer\TableTransformer;
use PhpBench\Pipeline\Splitter\RotarySplitter;
use PhpBench\Pipeline\Aggregation\SummaryAggregator;
use PhpBench\Pipeline\Parameters\SerialParameter;
use PhpBench\Pipeline\Transformer\AnsiRedrawOutputTransformer;
use PhpBench\Pipeline\Transformer\BarGraphTransformer;
use PhpBench\Pipeline\Gate\Batch;
use PhpBench\Pipeline\Transformer\ConcatTransformer;
use PhpBench\Pipeline\Sampler\CurlSampler;

require 'vendor/autoload.php';

$pipeline = new Pipeline([
    new Battery('∞'),
    new SerialParameter('url', [
        'https://inviqa.com',
        'https://www.bbc.co.uk',
        'https://www.google.de',
    ]),
    new CurlSampler([
        'url' => '%url%',
    ]),
    new SummaryAggregator([ 'url' ], [ 'total_time' ]),
    new RotarySplitter([
        new TableTransformer(),
        new BarGraphTransformer('url', 'total_time-mean'),
    ]),
    new Batch(2),
    new ConcatTransformer(),
    new AnsiRedrawOutputTransformer(),
    new StdOut(),
]);

$pipeline->run();
