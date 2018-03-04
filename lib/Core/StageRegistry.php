<?php

namespace PhpBench\Pipeline\Core;

use PhpBench\Pipeline\Core\Exception\UnknownStage;
use PhpBench\Pipeline\Core\Exception\StageAliasAlreadyRegistered;

class StageRegistry
{
    /**
     * @var array
     */
    private $stages = [];

    public function __construct(array $extensions)
    {
        /** @var PipelineExtension $extension */
        foreach ($extensions as $extension) {
            $this->addExtension($extension);
        }
    }

    public function get(string $name): Stage
    {
        if (!isset($this->stages[$name])) {
            throw new UnknownStage(sprintf(
                'Stage "%s" is not registered, registered stages: "%s"',
                $name, implode('", "', array_keys($this->stages))
            ));
        }

        return $this->stages[$name]->stage($name);
    }

    private function addExtension(PipelineExtension $extension)
    {
        foreach ($extension->stageAliases() as $alias) {
            if (isset($this->stages[$alias])) {
                throw new StageAliasAlreadyRegistered(sprintf(
                    'Stage "%s" is already registered by "%s" (when adding "%s")',
                    $alias,
                    get_class($this->stages[$alias]),
                    get_class($extension)
                ));
            }

            $this->stages[$alias] = $extension;
        }
    }
}
