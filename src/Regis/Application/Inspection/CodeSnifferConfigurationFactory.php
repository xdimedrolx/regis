<?php

declare(strict_types=1);

namespace Regis\Application\Inspection;

use Regis\Domain\Inspection\Rule;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class CodeSnifferConfigurationFactory implements ConfigurationFactory
{
    private $rules;

    public function __construct(CodeSnifferRules $rules)
    {
        $this->rules = $rules;
    }

    public function getConfigurationDefinition()
    {
        $treeBuilder = new TreeBuilder();

        $config = $treeBuilder->root('phpcs')
            ->addDefaultsIfNotSet()
            ->canBeDisabled()
        ;

        $rulesConfig = $config->children()->arrayNode('rules')->addDefaultsIfNotSet();

        /** @var Rule $rule */
        foreach ($this->rules->all() as $rule) {
            $rulesConfig
                ->children()
                    ->booleanNode($rule->getConfigKey())
                        ->info($rule->getDescription())
                        ->defaultTrue()
                    ->end()
                ->end();
        }

        return $config;
    }
}