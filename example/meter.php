<?php
use PhpBench\Pipeline\Output\StdOut;
use PhpBench\Pipeline\Pipeline;
use PhpBench\Pipeline\Battery;
use PhpBench\Pipeline\Transformer\TableTransformer;
use PhpBench\Pipeline\Splitter\RotarySplitter;
use PhpBench\Pipeline\Aggregation\SummaryAggregator;
use PhpBench\Pipeline\Parameters\SerialParameter;
use PhpBench\Pipeline\Transformer\AnsiRedrawOutputTransformer;
use PhpBench\Pipeline\Transformer\BarMeterTransformer;
use PhpBench\Pipeline\Gate\Batch;
use PhpBench\Pipeline\Transformer\ConcatTransformer;
use PhpBench\Pipeline\Sampler\CurlSampler;
use PhpBench\Pipeline\Parameters\RangeParameter;
use PhpBench\Pipeline\Aggregation\Collector;

require 'vendor/autoload.php';

$pipeline = new Pipeline([
    new Battery('∞'),
    new RangeParameter('count', 0, 1, 0.01),
    new Batch(100),
    new BarMeterTransformer('count', 'count', 5),
    new StdOut(),
]);

$pipeline->run();

