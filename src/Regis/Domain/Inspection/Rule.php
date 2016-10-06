<?php

declare(strict_types = 1);

namespace Regis\Domain\Inspection;

class Rule
{
    private $configKey;
    private $identifier;
    private $description;
    private $options = [];

    public function __construct(string $configKey, string $identifier, string $description, array $options = [])
    {
        $this->configKey = $configKey;
        $this->identifier = $identifier;
        $this->description = $description;
        $this->options = $options;
    }

    public function getConfigKey(): string
    {
        return $this->configKey;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}