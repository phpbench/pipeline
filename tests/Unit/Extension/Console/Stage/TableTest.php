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

        $output = $pipeline->build()->generator($input)->current();

        $this->assertEquals([ $expected ], $output);
    }

    public function provideTransformsInputIntoATable()
    {
        yield 'from single array element' => [
            [ 'hallo' ],
            <<<EOT
0     
-     
hallo 
EOT
        ];

        yield 'from single array element with key' => [
            [ 'tchuss' => 'hallo' ],
            <<<EOT
0     
-     
hallo 
EOT
        ];

        yield 'from single row, single column' => [
            [ 
                [ 'tchuss' => 'hallo' ],
            ],
            <<<EOT
tchuss 
------ 
hallo  
EOT
        ];

        yield 'from single row, multiple columns' => [
            [ 
                [ 'tchuss' => 'hallo', 'ciao' => 'goodbye' ],
            ],
            <<<EOT
tchuss ciao    
------ ----    
hallo  goodbye 
EOT
        ];

        yield 'from multiple rows, multiple columns' => [
            [ 
                [ 'tchuss' => 'hallo', 'ciao' => 'goodbye' ],
                [ 'tchuss' => 'bienvenu', 'ciao' => 'aurevoir' ],
            ],
            <<<EOT
tchuss   ciao     
------   ----     
hallo    goodbye  
bienvenu aurevoir 
EOT
        ];

        yield 'where header longer than value' => [
            [ 
                [ 'this is a header' => 'value' ],
            ],
            <<<EOT
this is a header 
---------------- 
value            
EOT
        ];

        yield 'non-scalar value' => [
            [
                [ 'blah' => ['value'] ],
            ],
            <<<EOT
blah      
----      
["value"] 
EOT
        ];
    }
}
