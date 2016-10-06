<?php

declare(strict_types=1);

namespace Regis\Infrastructure\Bundle\BackendBundle\Command;

use Regis\Application\Inspection\CodeSnifferConfigurationFactory;
use Regis\Application\Inspection\CodeSnifferRules;
use Regis\Domain\Model\Github\PullRequest;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Dumper\YamlReferenceDumper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InspectionsConfigurationReferenceCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('regis:inspections:configuration-reference')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configFactory = new CodeSnifferConfigurationFactory(new CodeSnifferRules());
        $dumper = new YamlReferenceDumper();

        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('inspections');

        $root->append($configFactory->getConfigurationDefinition());


        $output->writeln($dumper->dumpNode($treeBuilder->buildTree()));

        $inspector = $this->getContainer()->get('regis.inspector');
        $pr = PullRequest::fromArray([
            'repository' => [
                'clone_url' => 'nope',
                'owner' => 'K-Phoen',
                'name' => 'regis-test',
            ],
            'number' => 11,
            'revisions' => [
                'base' => '2bdf2a9ddb4ce629db94520b8433529402fb9dc5',
                'head' => '77b4a0af8e7c0824d77a1c2ffde32990f31f5aff',
            ]
        ]);

        $configuration = [
            'inspections' => [
                'phpcs' => [
                    'enabled' => true,
                    'rules' => [
                        'line_length' => false,
                        'disallow_space_indent' => false,
                    ]
                ]
            ]
        ];

        $inspector->inspect($pr->getRepository(), $pr->getRevisions(), $configuration);
    }
}
