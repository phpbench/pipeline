<?php

namespace PhpBench\Pipeline\Extension\Core\Stage\Sampler;

use PhpBench\Pipeline\Core\Stage;
use Generator;
use PhpBench\Pipeline\Core\Schema;

class CurlSampler implements Stage
{
    private $multiHandle;
    private $activeRequests = 0;

    public function __invoke(): Generator
    {
        list($config, $data) = yield;

        $this->multiHandle = curl_multi_init();

        while (true) {
            if ($this->activeRequests < $config['concurrency']) {
                $this->sampleUrl($config);
                $this->activeRequests++;
            }

            $status = curl_multi_exec($this->multiHandle, $active);
            $multiInfo = curl_multi_info_read($this->multiHandle);

            if (false !== $multiInfo) {
                $info = curl_getinfo($multiInfo['handle']);
                curl_multi_remove_handle($this->multiHandle, $multiInfo['handle']);
                curl_close($multiInfo['handle']);
                $this->activeRequests--;
                list($config, $data) = yield $info;
            }

            usleep(10000);
        }

        curl_multi_close($this->multiHandle);
    }

    public function configure(Schema $schema)
    {
        $schema->setRequired(['url']);
        $schema->setDefaults([
            'method' => 'GET',
            'headers' => [],
            'concurrency' => 1,
        ]);
    }

    private function sampleUrl(array $config): void
    {
        $handle = curl_init($config['url']);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_CUSTOMREQUEST, $config['method']);

        if ($config['headers']) {
            curl_setopt($handle, CURLOPT_HTTPHEADER, array_map(function ($key, $value) {
                return sprintf('%s: %s', $key, $value);
            }, array_keys($config['headers']), array_values($config['headers'])));
        }

        curl_multi_add_handle($this->multiHandle, $handle);
    }
}
