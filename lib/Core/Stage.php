<?php

namespace PhpBench\Pipeline\Core;

use Generator;

interface Stage
{
    public function __invoke(): Generator;

    public function configure(Schema $schema);
}
