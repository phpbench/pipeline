<?php
use PhpBench\Framework\Step\Take;
use PhpBench\Framework\Sampler\CallbackSampler;
use PhpBench\Framework\Pipeline;
use PhpBench\Framework\Scheduler\ParallelScheduler;
use PhpBench\Framework\Logger\StdOutLogger;
use PhpBench\Framework\Step\Timer;

require 'vendor/autoload.php';

$pipeline1 = new Pipeline([
    new Timer(1E6),
    new CallbackSampler('MD5 hash', function () {
        md5('Hello World');
    })
]);

$pipeline2 = new Pipeline([
    new Timer(1E6),
    new CallbackSampler('SHA1 hash', function () {
        sha1('Hello World');
    })
]);

$scheduler = new ParallelScheduler([
    $pipeline1,
    $pipeline2,
]);

$scheduler->run(new StdOutLogger());
