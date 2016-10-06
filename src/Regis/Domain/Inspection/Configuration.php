<?php

declare(strict_types = 1);

namespace Regis\Domain\Inspection;

class Configuration
{
    private $rules = [];

    public function addRule(Rule $rule)
    {
        $this->rules = $rule;
    }

    /**
     * @return Rule[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }
}