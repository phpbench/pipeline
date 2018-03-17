<?php

namespace PhpBench\Pipeline\Extension\Core\Stage\Sampler;

use PhpBench\Pipeline\Core\Stage;
use Generator;
use PhpBench\Pipeline\Core\Schema;
use PhpBench\Pipeline\Core\Signal;

class CurlSampler implements Stage
{
    private $multiHandle;

    public function __invoke(): Generator
    {
        list($config, $data) = yield;

        $this->multiHandle = curl_multi_init();
        $active = 0;

        while (true) {
            if ($active < $config['concurrency']) {
                $this->sampleUrl($config);
            }

            $multiInfo = curl_multi_info_read($this->multiHandle);
            curl_multi_exec($this->multiHandle, $active);

            if (false !== $multiInfo) {
                $info = $this->closeHandle($multiInfo);
                $info['concurrency'] = $active;
                list($config, $data) = yield $info;
            }

            if (true === $config['async']) {
                list($config, $data) = yield Signal::continue();
                continue;
            }

            usleep($config['sleep']);
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
            'async' => false,
            'sleep' => 10000,
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

    private function closeHandle($multiInfo)
    {
        $info = curl_getinfo($multiInfo['handle']);
        curl_multi_remove_handle($this->multiHandle, $multiInfo['handle']);
        curl_close($multiInfo['handle']);

        return $info;
    }
}
