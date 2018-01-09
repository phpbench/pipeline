<?php

namespace PhpBench\Framework\Tests\Unit\Transformer;

use PHPUnit\Framework\TestCase;
use PhpBench\Framework\Tests\Unit\StepTestCase;
use PhpBench\Framework\Transformer\TableTransformer;

class TableTransformerTest extends StepTestCase
{
    /**
     * @testdox Returns empty string if null given
     */
    public function testNull()
    {
        $result = $this->runStep(new TableTransformer(), [ null ]);
        $this->assertEquals([''], $result);
    }

    /**
     * @testdox Returns "table" for scalar
     */
    public function testScalar()
    {
        $result = $this->runStep(new TableTransformer(), [ 'foo' ]);
        $this->assertEquals([<<<'EOT'
0   
-   
foo 

EOT
], $result);
    }

    /**
     * @testdox Returns table for 1 dimsensional array
     */
    public function testOneDimensional()
    {
        $result = $this->runStep(new TableTransformer(), [ [ 'foo', 'bar' ] ]);
        $this->assertEquals([<<<'EOT'
0   
-   
foo 
bar 

EOT
], $result);
    }

    /**
     * @testdox Returns table and headers for 2 dimsensional array
     */
    public function testTwoDimensional()
    {
        $result = $this->runStep(new TableTransformer(), [ [ [ 'foo' => 'bar', 'bar' => 'foo' ] ] ]);
        $this->assertEquals([<<<'EOT'
foo bar 
--- --- 
bar foo 

EOT
], $result);
    }
}
