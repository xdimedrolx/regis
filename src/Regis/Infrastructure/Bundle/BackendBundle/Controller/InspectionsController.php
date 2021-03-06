<?php

declare(strict_types=1);

namespace Regis\Infrastructure\Bundle\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Regis\Application\Command;
use Regis\Domain\Entity;
use Regis\Domain\Model\Git\Repository;
use Regis\Domain\Model\Github\PullRequest;

class InspectionsController extends Controller
{
    public function retryAction(Entity\Github\PullRequestInspection $inspection)
    {
        /** @var Entity\Github\Repository $repository */
        $repository = $inspection->getRepository();

        $command = new Command\Github\Inspection\SchedulePullRequest(new PullRequest(
            new Repository($repository->getOwnerUsername(), $repository->getName(), 'we don\'t have the clone URL, lets hope it is already cloned by now.'),
            $inspection->getPullRequestNumber(),
            $inspection->getRevisions()
        ));

        $this->get('tactician.commandbus')->handle($command);

        $this->addFlash('info', 'Inspection retried.');

        return $this->redirectToRoute('repositories_detail', ['identifier' => $repository->getIdentifier()]);
    }

    public function detailAction(Entity\Github\PullRequestInspection $inspection)
    {
        return $this->render('@RegisBackend/Inspections/detail.html.twig', [
            'inspection' => $inspection,
        ]);
    }
}