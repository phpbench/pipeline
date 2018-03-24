<?php

namespace PhpBench\Pipeline\Extension\Console\Terminal;

class Terminal
{
    const ANSI_SAVE_CURSOR_POS = "\033[s";
    const ANSI_RESTORE_CURSOR_POS = "\033[u";

    public function dimensions(): Dimensions
    {
        static $lines;
        static $columns;

        if (null === $lines) {
            $lines = exec('tput lines');
        }

        if (null === $columns) {
            $columns = exec('tput cols');
        }

        $dim = Dimensions::fromWidthAndHeight($columns, $lines);

        return $dim;
    }

    public function moveCursorUp(int $amount): string
    {
        return "\033[" . $amount . "A";
    }

    public function moveCursorBackward(int $amount): string
    {
        return "\033[" . $amount . "D";
    }
}
