<?php

namespace PhpBench\Pipeline\Tests\Unit;

use Symfony\Component\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;

class PipelineTestCase extends TestCase
{
    public function workspaceDir()
    {
        return __DIR__ . '/../Workspace';
    }

    public function workspacePath(string $path)
    {
        return $this->workspaceDir() . '/' . $path;
    }

    public function clearWorkspace()
    {
        if (file_exists($this->workspaceDir())) {
            $filesystem = new Filesystem();
            $filesystem->remove($this->workspaceDir());
        }

        mkdir($this->workspaceDir());
    }
}
