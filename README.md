PHPBench Pipeline
=================

Composable micro benchmarking framework.

Usage

```php
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
```

```bash
url                   http_code total_time-mean total_time-min total_time-max total_time-stdev
---                   --------- --------------- -------------- -------------- ----------------
https://inviqa.com    200       0.3389065       0.260603       0.41721        0.11073787168128
https://www.bbc.co.uk 200       0.996427        0.996427       0.996427       0 
https://www.google.de 200       0.336525        0.336525       0.336525       0    

https://inviqa.com     |████████████████████▏                              0.33911 
https://www.bbc.co.uk  |██████████████████████████████████████████████████ 0.838263
https://www.google.de  |██████████████████▊                                0.315372
```
