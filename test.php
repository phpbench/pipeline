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

$pipeline = new Pipeline([
    new Generator('∞'),
    new Parameters([
        'url' => 'http://www.dantleech.com',
    ]),
    new Splitter([
        new Group([
            new Take(10),
            new HttpSampler([
                'url' => '%url%',
            ]),
        ]),
        new CallbackSampler([
            'callback' => function (array $params) {
                md5($params['url']);
            },
        ]),
    ]),
    new TableEncoder(),
    new AnsiCursorReset(),
    new StdOut()
]);

$step = $pipeline->pop(); // "StdOut"
$generator = $step->generator($pipeline);

foreach ($generator as $result) {
    // ∞
}
