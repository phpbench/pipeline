<?php

namespace PhpBench\Pipeline\Extension\Console\Terminal;

final class Dimensions
{
    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    public function __construct(?int $width, ?int $height)
    {
        $this->width = $width;
        $this->height = $height;
    }

    public function height(): int
    {
        return $this->height;
    }

    public function width(): int
    {
        return $this->width;
    }

    public function hasHeight(): bool
    {
        return null !== $this->height;
    }

    public function hasWidth(): bool
    {
        return null !== $this->width;
    }

    public static function fromWidthAndHeight(?int $width, ?int $height): self
    {
        return new self($width, $height);
    }

}
