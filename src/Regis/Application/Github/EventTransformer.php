<?php

declare(strict_types=1);

namespace Regis\Application\Github;

use Symfony\Component\HttpFoundation\Request;

use Regis\Application\Event;
use Regis\Domain\Model;

/**
 * Transforms a payload sent by Github in a domain event.
 */
class EventTransformer
{
    const TYPE_PULL_REQUEST = 'pull_request';
    const ACTION_OPEN_PULL_REQUEST = 'opened';
    const ACTION_CLOSE_PULL_REQUEST = 'closed';
    const ACTION_SYNC_PULL_REQUEST = 'synchronize';

    public function transform(Request $request)
    {
        $eventType = $request->headers->get('X-GitHub-Event');

        if ($eventType === self::TYPE_PULL_REQUEST) {
            $payload = json_decode($request->getContent(), true);

            if ($payload['action'] === self::ACTION_OPEN_PULL_REQUEST) {
                return $this->transformPullRequestOpened($payload);
            } else if ($payload['action'] === self::ACTION_SYNC_PULL_REQUEST) {
                return $this->transformPullRequestSynced($payload);
            } else if ($payload['action'] === self::ACTION_CLOSE_PULL_REQUEST) {
                return $this->transformPullRequestClosed($payload);
            }
        }

        throw new Exception\EventNotHandled(sprintf('Event of type "%s" not handled.', $eventType));
    }

    private function transformPullRequestOpened(array $payload): Event\PullRequestOpened
    {
        $pullRequest = $this->transformPullRequest($payload);

        return new Event\PullRequestOpened($pullRequest);
    }

    private function transformPullRequestClosed(array $payload): Event\PullRequestClosed
    {
        $pullRequest = $this->transformPullRequest($payload);

        return new Event\PullRequestClosed($pullRequest);
    }

    private function transformPullRequestSynced(array $payload): Event\PullRequestSynced
    {
        $pullRequest = $this->transformPullRequest($payload);

        return new Event\PullRequestSynced($pullRequest, $payload['before'], $payload['after']);
    }

    private function transformPullRequest(array $payload): Model\Github\PullRequest
    {
        $repository = new Model\Git\Repository(
            $payload['repository']['owner']['login'],
            $payload['repository']['name'],
            $payload['repository']['private'] ? $payload['repository']['ssh_url'] : $payload['repository']['clone_url']
        );

        return new Model\Github\PullRequest(
            $repository, (int) $payload['number'],
            new Model\Git\Revisions($payload['pull_request']['base']['sha'], $payload['pull_request']['head']['sha'])
        );
    }
}