<?php

namespace PhpBench\Pipeline\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use PhpBench\Pipeline\Core\Stage;
use PhpBench\Pipeline\Core\Schema;

class StageTestCase extends TestCase
{
    protected function runStage(Stage $stage, array $config, array $initial)
    {
        $schema = new Schema();
        $stage->configure($schema);
        $config = $schema->resolve($config);
        $generator = $stage($config);
        return $generator->send($initial);
    }
}
