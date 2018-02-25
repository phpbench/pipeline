<?php

namespace PhpBench\Pipeline\Core;

use Generator;
use PhpBench\Pipeline\Exception\StageMustBeCallable;
use PhpBench\Pipeline\Exception\StageMustCreateGenerator;
use PhpBench\Pipeline\Core\Stage;
use PhpBench\Pipeline\Core\Schema;
use Countable;
use PhpBench\Pipeline\Core\Exception\GeneratorMustYieldAnArray;

class Pipeline implements Stage, Countable
{
    /**
     * @var Generator[]
     */
    private $generators;

    public function __construct(array $generators)
    {
        foreach ($generators as $generator) {
            $this->add($generator);
        }
    }

    public function __invoke(array $config = []): Generator
    {
        if (empty($this->generators)) {
            return;
        }

        // get the input
        $data = yield;

        // repeat until one of the generators is exhausted (not valid)
        while (true) {
            // run all of the stage generators sequentially, passing the result
            // of the previous stage to the next stage.
            for ($index = 0; $index < count($this->generators); $index++) {
                $generator = $this->generators[$index];
                $data = $generator->send($data);

                if (false === $generator->valid()) {
                    return $data;
                }

                if (false === is_array($data)) {
                    throw new GeneratorMustYieldAnArray($data);
                }
            }

            // yield the last result
            yield $data;
        }
    }

    /**
     * Run the pipeline.
     *
     * Run all of the stages in the pipeline sequentially.
     * An initial value can be passed.
     */
    public function run(array $data = [])
    {
        $generator = $this->__invoke();

        // send the initial value and trigger the first iteration
        $generator->send($data);

        // iterate the rest of the pipeline
        while ($generator->valid()) {
            $data = $generator->current();
            $generator->next();
        }

        return $data;
    }

    private function add(Generator $generator)
    {
        $this->generators[] = $generator;
    }

    public function configure(Schema $schema)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return count($this->generators);
    }
}
