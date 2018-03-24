<?php

namespace PhpBench\Pipeline\Extension\Console;

use PhpBench\Pipeline\Core\PipelineExtension;
use PhpBench\Pipeline\Core\Stage;
use PhpBench\Pipeline\Extension\Console\Stage\Redraw;
use PhpBench\Pipeline\Extension\Console\Stage\Table;

class ConsoleExtension implements PipelineExtension
{
    /**
     * @var array
     */
    private $stages;

    public function __construct()
    {
        $this->stages = [
            'console/redraw' => new Redraw(),
            'console/table' => new Table(),
        ];
    }

    public function stageAliases(): array
    {
        return array_keys($this->stages);
    }

    public function stage(string $alias): Stage
    {
        return $this->stages[$alias];
    }
}
