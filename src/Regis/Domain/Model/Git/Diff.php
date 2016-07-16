<?php

declare(strict_types=1);

namespace Regis\Domain\Model\Git;

class Diff
{
    private $revisions;
    private $files;
    private $rawDiff;

    public function __construct(Revisions $revisions, array $files, string $rawDiff)
    {
        $this->revisions = $revisions;
        $this->files = $files;
        $this->rawDiff = $rawDiff;
    }

    public function getBase(): string
    {
        return $this->revisions->getBase();
    }

    public function getHead(): string
    {
        return $this->revisions->getHead();
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function getRawDiff(): string
    {
        return $this->rawDiff;
    }

    public function getAddedTextFiles(): \Traversable
    {
        foreach ($this->files as $file) {
            if ($file->isBinary() || $file->isRename() || $file->isDeletion()) {
                continue;
            }

            yield $file;
        }
    }
}