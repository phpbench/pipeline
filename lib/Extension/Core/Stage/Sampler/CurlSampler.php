<?php

namespace PhpBench\Pipeline\Extension\Core\Stage\Sampler;

use PhpBench\Pipeline\Core\Stage;
use Generator;
use PhpBench\Pipeline\Core\Schema;

class CurlSampler implements Stage
{
    public function __invoke(array $config): Generator
    {
        $data = yield;

        while (true) {
            $data = yield $this->sampleUrl($config);
        }
    }

    public function configure(Schema $schema)
    {
        $schema->setRequired(['url']);
        $schema->setDefaults([
            'method' => 'GET',
            'headers' => [],
        ]);
    }

    private function sampleUrl(array $config): array
    {
        $handle = curl_init($config['url']);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_CUSTOMREQUEST, $config['method']);

        if ($config['headers']) {
            curl_setopt($handle, CURLOPT_HTTPHEADER, array_map(function ($key, $value) {
                return sprintf('%s: %s', $key, $value);
            }, array_keys($config['headers']), array_values($config['headers'])));
        }

        curl_exec($handle);
        $info = curl_getinfo($handle);
        curl_close($handle);
        return $info;
    }
}
