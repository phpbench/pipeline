<?php

namespace PhpBench\Pipeline\Tests\Unit;

use PHPUnit\Framework\TestCase;
use PhpBench\Pipeline\Core\Stage;
use PhpBench\Pipeline\Core\Schema;

class StageTestCase extends TestCase
{
    protected function runStage(Stage $stage, array $config, array $initial, int $nbTimes = 1)
    {
        $schema = new Schema();
        $stage->configure($schema);
        $config = $schema->resolve($config);
        $generator = $stage($config);


        for ($i = 0; $i < $nbTimes; $i++) {
            $data = $generator->send($initial);
        }

        return $data;
    }
}
