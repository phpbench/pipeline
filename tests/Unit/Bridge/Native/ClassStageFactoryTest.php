<?php

namespace PhpBench\Pipeline\Tests\Unit\Bridge\Native;

use PHPUnit\Framework\TestCase;
use PhpBench\Pipeline\Bridge\Native\ClassStageFactory;
use PhpBench\Pipeline\Bridge\Native\Exception\ClassNotFound;
use stdClass;
use PhpBench\Pipeline\Bridge\Native\Exception\ClassNotInstanceOfStage;
use PhpBench\Pipeline\Core\Stage;

class ClassStageFactoryTest extends TestCase
{
    /**
     * @var ClassStageFactory
     */
    private $factory;

    public function setUp()
    {
        $this->factory = new ClassStageFactory();
    }

    public function testThrowsExceptionIfClassNotFound()
    {
        $this->expectException(ClassNotFound::class);
        $this->expectExceptionMessage('Class "IDontExist" not found');
        $this->factory->create('IDontExist');
    }

    public function testExceptionIfNotInstanceOfStage()
    {
        $this->expectException(ClassNotInstanceOfStage::class);
        $this->expectExceptionMessage('Class "stdClass" is not an instance of PhpBench\Pipeline\Stage');
        $this->factory->create(stdClass::class);
    }

    public function testInstantiatesStage()
    {
        $stage = $this->prophesize(Stage::class);

        $stage = $this->factory->create(get_class($stage->reveal()));
        $this->assertInstanceOf(Stage::class, $stage);
    }
}
