<?php

declare(strict_types=1);

namespace Regis\Application;

use Regis\Application\Inspection\ConfigurationFactory;
use Regis\Domain\Model;

interface Inspection
{
    function getType(): string;

    function getConfigurationFactory(): ConfigurationFactory;

    function inspectDiff(Model\Git\Diff $diff, array $config): \Traversable;
}