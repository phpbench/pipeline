<?php

namespace PhpBench\Pipeline\Extension\Console\Stage;

use PhpBench\Pipeline\Core\Stage;
use PhpBench\Pipeline\Extension\Console\Util\ConsoleUtil;
use Generator;
use PhpBench\Pipeline\Core\Schema;

class Redraw implements Stage
{
    const ANSI_SAVE_CURSOR_POS = "\033[s";
    const ANSI_RESTORE_CURSOR_POS = "\033[u";

    public function __invoke(): Generator
    {
        list($config, $data) = yield;

        $lastData = null;
        $lineLength = 0;

        while (true) {
            $firstLine = true;

            foreach ($data as &$text) {
                if (false === is_string($text)) {
                    $text = json_encode($text);
                }

                if (null === $lastData) {
                    $text = self::ANSI_SAVE_CURSOR_POS.$text;
                    break;
                }

                $text = self::ANSI_RESTORE_CURSOR_POS.$text;
                $lineLength = $this->maxLineLength($text, $lineLength);
                $text = $this->maximizeLines($text, $lineLength);
                break;
            }

            list($config, $data) = yield $data;
            $lastData = $data;
        }
    }

    private function maxLineLength(string $result, int $maxLineLength)
    {
        foreach (explode(PHP_EOL, $result) as $line) {
            $length = mb_strlen($line);
            if ($length > $maxLineLength) {
                $maxLineLength = $length;
            }
        }

        return $maxLineLength;
    }

    private function maximizeLines(string $result, int $maxLineLength)
    {
        $result = trim($result, PHP_EOL);
        $lines = explode(PHP_EOL, $result);

        foreach ($lines as &$line) {
            $line = ConsoleUtil::pad($line, $maxLineLength);
        }

        return implode(PHP_EOL, $lines);
    }

    public function configure(Schema $schema)
    {
    }
}
