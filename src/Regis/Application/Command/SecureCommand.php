<?php

declare(strict_types = 1);

namespace Regis\Application\Command;

use Regis\Domain\Entity;

interface SecureCommand
{
    function executionAuthorizedFor(Entity\User $user): bool;
}