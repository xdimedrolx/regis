<?php

declare(strict_types=1);

namespace Regis\Infrastructure\CodeSniffer;

use Regis\Application\Inspection\CodeSnifferRules;

class ConfigurationDumper
{
    private $rules;
    private $tmpDir;

    public function __construct(CodeSnifferRules $rules, string $tmpDir = null)
    {
        $this->rules = $rules;
        $this->tmpDir = $tmpDir ?: sys_get_temp_dir();
    }

    /**
     * @param array $config
     *
     * @return string Path to the dumped configuration.
     */
    public function dump(array $config)
    {
        $fileName = $this->tmpDir . '/' . uniqid('codesniffer_config_', true).'.xml';

        file_put_contents($fileName, $this->convertConfig($config));

        return $fileName;
    }

    private function convertConfig(array $config)
    {
        $xml = <<<XML
<?xml version="1.0"?>
<ruleset name="regis">

XML;

        foreach ($config['rules'] as $name => $enabled) {
            $rule = $this->rules->get($name);
            $ruleIdentifier = $rule->getIdentifier();

            if ($enabled) {
                $xml .= sprintf('<rule ref="%s" />', $ruleIdentifier) . PHP_EOL;
                continue;
            }

            $fromStandard = substr($ruleIdentifier, 0, strpos($ruleIdentifier, '.'));

            $xml .= sprintf("<rule ref=\"%s\">\n\t<exclude name=\"%s\" />\n</rule>\n", $fromStandard, $ruleIdentifier);
        }

        return $xml .'</ruleset>';
    }
}