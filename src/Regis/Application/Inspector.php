<?php

declare(strict_types=1);

namespace Regis\Application;

use Gitonomy\Git as Gitonomy;
use Regis\Domain\Entity;
use Regis\Domain\Model;
use Regis\Infrastructure\Vcs\Git;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;

class Inspector
{
    private $git;
    /** @var Inspection[] */
    private $inspections;

    public function __construct(Git $git, array $inspections = [])
    {
        $this->git = $git;
        $this->inspections = $inspections;
    }

    public function inspect(Model\Git\Repository $repository, Model\Git\Revisions $revisions, array $configuration): Entity\Inspection\Report
    {
        $gitRepository = $this->git->getRepository($repository);
        $gitRepository->update();

        $diff = $gitRepository->getDiff($revisions);

        return $this->inspectDiff($diff, $configuration);
    }

    private function inspectDiff(Model\Git\Diff $diff, array $configuration): Entity\Inspection\Report
    {
        $report = new Entity\Inspection\Report($diff->getRawDiff());

        foreach ($this->inspections as $inspection) {
            $analysis = new Entity\Inspection\Analysis($inspection->getType());
            $inspectionConfig = $this->prepareInspectionConfiguration($inspection, $configuration);

            foreach ($inspection->inspectDiff($diff, $inspectionConfig) as $violation) {
                var_dump($violation);
                $analysis->addViolation($violation);
            }

            $report->addAnalysis($analysis);
        }

        return $report;
    }

    /**
     * @TODO rewrite
     */
    private function prepareInspectionConfiguration(Inspection $inspection, array $configuration)
    {
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('inspections');

        $configDefinition = $inspection->getConfigurationFactory()->getConfigurationDefinition();
        $root->append($configDefinition);

        $processor = new Processor();

        $config = $processor->process($treeBuilder->buildTree(), $configuration);

        return $config[$inspection->getType()];
    }
}