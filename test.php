<?php
use PhpBench\Framework\Gate\Take;
use PhpBench\Framework\Sampler\CallbackSampler;
use PhpBench\Framework\Circuit;
use PhpBench\Framework\Scheduler\ParallelScheduler;
use PhpBench\Framework\Logger\StdOutLogger;
use PhpBench\Framework\Gate\Timeout;
use PhpBench\Framework\Splitter\Splitter;
use PhpBench\Framework\ResultAggregator;
use PhpBench\Framework\Gate\Collector;
use PhpBench\Framework\Encoder\JsonEncoder;
use PhpBench\Framework\Logger\AnsiResetLine;
use PhpBench\Framework\Encoder\TableEncoder;

require 'vendor/autoload.php';

$pipeline = new Circuit([
    new StdOutLogger(),
    //new AnsiResetLine(),
    new JsonEncoder(),
    //new TableEncoder(),
    //new Collector(),

    new Splitter([
        new Circuit([
            new ResultAggregator('microseconds'),
        ]),
    ]),

    new Take(400),
    //new Timeout(1E6),
    new ParallelScheduler([
        new CallbackSampler('MD5 hash', function () {
            md5('Hello World');
        }, 100000),
        new CallbackSampler('SHA1 hash', function () {
            sha1('Hello World');
        }, 100000),
        new Circuit([
            new Take(2),
            new CallbackSampler('FOO hash', function () {
                sha1('Hello World');
            }, 100000),
        ])
    ])
]);

$pipeline->run();


