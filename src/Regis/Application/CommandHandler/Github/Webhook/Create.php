<?php

declare(strict_types=1);

namespace Regis\Application\CommandHandler\Github\Webhook;

use Regis\Application\Command;
use Regis\Application\Github\ClientFactory as GithubClientFactory;
use Regis\Domain\Entity;
use Regis\Domain\Repository\Repositories;

class Create
{
    private $githubClientFactory;
    private $repositoriesRepo;

    public function __construct(GithubClientFactory $githubClientFactory, Repositories $repositoriesRepo)
    {
        $this->githubClientFactory = $githubClientFactory;
        $this->repositoriesRepo = $repositoriesRepo;
    }

    public function handle(Command\Github\Webhook\Create $command)
    {
        /** @var Entity\Github\Repository $repository */
        $repository = $this->repositoriesRepo->find($command->getOwner().'/'.$command->getRepo());
        $githubClient = $this->githubClientFactory->createForRepository($repository);

        $githubClient->createWebhook(
            $command->getOwner(),
            $command->getRepo(),
            $command->getCallbackUrl(),
            $repository->getSharedSecret()
        );
    }
}
