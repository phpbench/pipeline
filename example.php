<?php

$benchmark1 = new Benchmark(
    new Named('MD5'),
    new SimpleIterator(50),
    new ParameterIterator([
        [ 'Hello' ],
        [ 'Goodbye' ],
    ]),
    new Revolutions(1000),
    new CallbackExecutor(function ($text) { md5($text); })
);

$benchmark2 = new Benchmark(
    new Named('SHA256'),
    new SimpleIterator(50),
    new ParameterIterator([
        [ 'Hello' ],
        [ 'Goodbye' ],
    ]),
    new Revolutions(1000),
    new CallbackExecutor(function ($text) { hash('sha256', $text); })
);

$runner = new LinearRunner(
    $runner->run($benchmark1, $benchmark2)
);
$runner = new SequentialRunner(
    $runner->run($benchmark1, $benchmark2)
);
$runner = new RandomRunner(
    $runner->run($benchmark1, $benchmark2)
);

$runner->run(new StdOutLogger());
