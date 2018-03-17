<?php

namespace PhpBench\Pipeline\Core;

final class Signal
{
    private $signal;

    private function __construct(string $signal)
    {
        $this->signal = $signal;
    }

    public static function continue()
    {
        return new self('continue');
    }
}
