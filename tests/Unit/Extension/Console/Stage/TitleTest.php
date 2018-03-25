<?php

namespace PhpBench\Pipeline\Tests\Unit\Extension\Console\Stage;

use PhpBench\Pipeline\Tests\Unit\Extension\Console\ConsoleTestCase;

class TitleTest extends ConsoleTestCase
{
    public function testUnderlinedTitleText()
    {
        $pipeline = $this->pipeline()
            ->stage('console/title', [
                'text' => 'Hello World',
            ]);

        $output = $pipeline->build()->generator([])->current();

        $this->assertEquals([ <<<EOT
Hello World
===========
EOT
], $output);
    }
}
