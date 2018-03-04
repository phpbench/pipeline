<?php

namespace PhpBench\Pipeline\Core;

use Generator;
use PhpBench\Pipeline\Core\Schema;

interface Stage
{
    public function __invoke(array $config): Generator;

    public function configure(Schema $schema);
}
