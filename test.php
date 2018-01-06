<?php
use PhpBench\Framework\Valve\Take;
use PhpBench\Framework\Sampler\CallbackSampler;
use PhpBench\Framework\Pipeline;
use PhpBench\Framework\Scheduler\ParallelScheduler;
use PhpBench\Framework\Logger\StdOutLogger;
use PhpBench\Framework\Valve\Timeout;

require 'vendor/autoload.php';

$pipeline = new Pipeline([
    new StdOutLogger(),
    new Take(20),
    new Timeout(1E6),
    new ParallelScheduler([
        new CallbackSampler('MD5 hash', function () {
            md5('Hello World');
        }),
        new CallbackSampler('SHA1 hash', function () {
            sha1('Hello World');
        }),
        new Pipeline([
            new Take(2),
            new CallbackSampler('FOO hash', function () {
                sha1('Hello World');
            }),
        ])
    ])
]);

$pipeline->run();
