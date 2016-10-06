<?php

declare(strict_types=1);

namespace Regis\Application\Inspection;

interface ConfigurationFactory
{
    /**
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    function getConfigurationDefinition();
}