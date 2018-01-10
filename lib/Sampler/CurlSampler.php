<?php

namespace PhpBench\Framework\Sampler;

use PhpBench\Framework\Config\ConfigBuilder;
use PhpBench\Framework\Step;
use PhpBench\Framework\Config\Config;
use Generator;
use PhpBench\Framework\Pipeline;

class CurlSampler implements Step
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(array $config)
    {
        $this->config = ConfigBuilder::create(__CLASS__)
            ->defaults([
                'url' => 'http://www.example.com',
                'capture' => [
                    'http_code',
                    'total_time',
                ],
            ])
            ->build($config);
    }

    public function generator(Pipeline $pipeline): Generator
    {
        foreach ($pipeline->pop() as $data) {
            $url = $this->config->resolve('url', $data);
            $handle = curl_init($url);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            curl_exec($handle);
            $info = curl_getinfo($handle);
            curl_close($handle);

            yield array_merge([
                'url' => $url,
            ], array_filter($info, function ($infoKey) {
                return in_array($infoKey, $this->config['capture']);
            }, ARRAY_FILTER_USE_KEY));
        }
    }
}
