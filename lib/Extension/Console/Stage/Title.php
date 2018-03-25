<?php

namespace PhpBench\Pipeline\Extension\Console\Stage;

use PhpBench\Pipeline\Core\Stage;
use Generator;
use PhpBench\Pipeline\Core\Schema;

class Title implements Stage
{
    public function __invoke(): Generator
    {
        list($config, $data) = yield;
        while (true) {
            list($config, $data) = yield [
                $this->title($config['text']),
            ];
        }
    }

    public function configure(Schema $schema)
    {
        $schema->setRequired([
            'text'
        ]);
        $schema->setTypes([
            'text' => 'string'
        ]);
    }

    private function title(string $text): string
    {
        return implode(PHP_EOL, [
            $text,
            str_repeat('=', mb_strlen($text))
        ]);
    }
}
