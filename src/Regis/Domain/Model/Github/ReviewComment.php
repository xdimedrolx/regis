<?php

declare(strict_types=1);

namespace Regis\Domain\Model\Github;

use Regis\Domain\Entity\Inspection\Violation;

class ReviewComment
{
    private $content;
    private $position;
    private $file;

    public static function fromViolation(Violation $violation): ReviewComment
    {
        return new static($violation->getFile(), $violation->getPosition(), $violation->getDescription());
    }

    public function __construct(string $file, int $position, string $content)
    {
        $this->file = $file;
        $this->position = $position;
        $this->content = $content;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getFile(): string
    {
        return $this->file;
    }
}