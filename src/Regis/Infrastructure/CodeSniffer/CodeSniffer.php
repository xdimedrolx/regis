<?php

declare(strict_types=1);

namespace Regis\Infrastructure\CodeSniffer;

use Symfony\Component\Process\Process;
use Regis\Application\Inspection\CodeSnifferRunner;

class CodeSniffer implements CodeSnifferRunner
{
    private $phpcsBin;
    private $configurationDumper;

    public function __construct(string $phpCsBin, ConfigurationDumper $configurationDumper)
    {
        $this->phpcsBin = $phpCsBin;
        $this->configurationDumper = $configurationDumper;
    }

    public function execute(string $fileName, string $fileContent, array $config): array
    {
        $configFile = $this->configurationDumper->dump($config);

        try {
            $process = new Process(sprintf(
                '%s --standard=%s --report=json --stdin-path=%s',
                escapeshellarg($this->phpcsBin),
                $configFile,
                escapeshellarg($fileName)
            ));

            $process->setInput($fileContent);
            $process->run();
        } finally {
            unlink($configFile);
        }

        return json_decode($process->getOutput(), true);
    }
}