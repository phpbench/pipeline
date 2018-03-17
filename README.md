PHPBench Pipeline
=================

[![Build
Status](https://travis-ci.org/phpbench/pipeline.svg?branch=master)](https://travis-ci.org/phpbench/pipeline)

Composable performance sampling framework.

```php
use PhpBench\Pipeline\Core\PipelineBuilder;

$builder = PipelineBuilder::createWithDefaults()
    ->stage('parameter/serial', [
        'name' => 'algo',
        'values' => hash_algos(),
    ])
    ->stage('sampler/callable', [
        'callable' => function ($data) { hash($data['algo'], 'Hello World'); },
        'iterations' => 100,
    ])
    ->stage('encoder/json')
    ->stage('output/stream')

    ->build()
    ->run();
```
