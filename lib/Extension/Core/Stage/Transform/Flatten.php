<?php

namespace PhpBench\Pipeline\Extension\Core\Stage\Transform;

use PhpBench\Pipeline\Core\Stage;
use Generator;
use PhpBench\Pipeline\Core\Schema;

class Flatten implements Stage
{
    public function __invoke(): Generator
    {
        list($config, $data) = yield;

        while (true) {
            $data = $this->flatten($data, '', $config['level']);
            list ($config, $data) = yield $data;
        }
    }

    private function flatten(array $data, string $prefix, $startLevel, $level = 0)
    {
        $flattened = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if ($level < $startLevel) {
                    $flattened[$key] = $this->flatten($value, '', $startLevel, $level + 1);
                    continue;
                }

                $flattened = array_merge($flattened, $this->flatten($value, $prefix . $key . '_', $startLevel, $level + 1));
                continue;
            }

            $flattened[$prefix . $key] = $value;
        }

        return $flattened;
    }

    public function configure(Schema $schema)
    {
        $schema->setDefaults([
            'level' => 0,
        ]);
    }
}
