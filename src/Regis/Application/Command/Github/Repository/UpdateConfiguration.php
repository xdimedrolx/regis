<?php

declare(strict_types=1);

namespace Regis\Application\Command\Github\Repository;

use Regis\Domain\Entity;

class UpdateConfiguration
{
    private $repository;
    private $newSharedSecret;

    public function __construct(Entity\Github\Repository $repository, string $newSharedSecret)
    {
        $this->repository = $repository;
        $this->newSharedSecret = $newSharedSecret;
    }

    public function getRepository(): Entity\Github\Repository
    {
        return $this->repository;
    }

    public function getNewSharedSecret(): string
    {
        return $this->newSharedSecret;
    }
}