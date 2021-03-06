<?php

namespace PhpBench\Pipeline\Extension\Console\Util;

class ConsoleUtil
{
    public static function pad(string $string, int $length)
    {
        $width = $length - mb_strlen($string);

        return $string.str_repeat(' ', $width);
    }
}
