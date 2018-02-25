<?php

namespace PhpBench\Pipeline\Bridge\Native;

use PhpBench\Pipeline\Core\StageFactory;
use PhpBench\Pipeline\Core\Stage;
use PhpBench\Pipeline\Bridge\Native\Exception\ClassNotFound;
use PhpBench\Pipeline\Bridge\Native\Exception\ClassNotInstanceOfStage;

class ClassStageFactory implements StageFactory
{
    public function create(string $type): Stage
    {
        if (false === class_exists($type)) {
            throw new ClassNotFound($type);
        }

        $stage = new $type;

        if (false === $stage instanceof Stage) {
            throw new ClassNotInstanceOfStage($type);
        }

        return $stage;
    }
}
