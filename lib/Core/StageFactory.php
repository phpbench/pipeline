<?php

namespace PhpBench\Pipeline\Core;

use PhpBench\Pipeline\Core\Stage;

interface StageFactory
{
    public function create(string $type): Stage;
}
