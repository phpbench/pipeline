<?php

namespace PhpBench\Framework;

use PhpBench\Framework\Result;

class TimeResult implements Result
{
    /**
     * @var int
     */
    private $microseconds;

    public function __construct(int $microseconds)
    {
        $this->microseconds = $microseconds;
    }

    public static function fromMicrotimeStartEnd(float $start, float $end)
    {
        return new self(($end * 1E6) - ($start * 1E6));
    }

    public function microseconds()
    {
        return $this->microseconds;
    }

    public function toArray(): array
    {
        return ['value' => $this->microseconds ];
    }
}
