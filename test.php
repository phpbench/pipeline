<?php
use PhpBench\Framework\Output\StdOut;
use PhpBench\Framework\Pipeline;
use PhpBench\Framework\Sampler\CallbackSampler;
use PhpBench\Framework\Battery;
use PhpBench\Framework\Parameters\FixedParameters;
use PhpBench\Framework\Gate\QuantityGate;
use PhpBench\Framework\Transformer\JsonTransformer;

require 'vendor/autoload.php';

$pipeline = new Pipeline([
    new Battery('∞'),
    new FixedParameters([
        'url' => 'http://www.google.com',
    ]),
    new CallbackSampler([
        'callback' => function (array $params) {
            usleep(100000);
        },
        'label' => 'PHPBench dot org',
    ]),
    new QuantityGate(10),
    new JsonTransformer(),
    new StdOut()
]);

$generator = $pipeline->pop(); // "StdOut"

foreach ($generator as $result) {
    // ∞
}
