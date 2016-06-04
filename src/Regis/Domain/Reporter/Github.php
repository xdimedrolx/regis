<?php

declare(strict_types=1);

namespace Regis\Domain\Reporter;

use Regis\Domain\Model;
use Regis\Domain\Reporter;
use Regis\Github\Client;

class Github implements Reporter
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function report(Model\Violation $violation, Model\Github\PullRequest $pullRequest)
    {
        $this->client->sendComment($pullRequest, Model\Github\ReviewComment::fromViolation($violation));
    }
}