<?php

namespace PhpBench\Pipeline\Tests\Unit\Extension\Console\Stage;

use PhpBench\Pipeline\Tests\Unit\Extension\Console\ConsoleTestCase;

class TableTest extends ConsoleTestCase
{
    /**
     * @dataProvider provideTransformsInputIntoATable
     */
    public function testTransformsInputIntoATable(array $input, string $expected)
    {
        $pipeline = $this->pipeline()
            ->stage('console/table');

        $output = $pipeline->run($input);

        $this->assertEquals($expected, $output);
    }

    public function provideTransformsInputIntoATable()
    {
        yield 'single array element' => [
            [ 'hallo' ],
            <<<EOT
0
-----
hallo
EOT
        ];
    }
}
