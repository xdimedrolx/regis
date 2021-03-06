<?php

namespace Tests\Regis\Domain\Model\Git;

use Regis\Domain\Model\Git;

class   RepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function testItCanBeConstructedFromAnArray()
    {
        $repository = Git\Repository::fromArray([
            'owner' => 'K-Phoen',
            'name' => 'test',
            'clone_url' => 'clone url',
        ]);

        $this->assertEquals('K-Phoen', $repository->getOwner());
        $this->assertEquals('test', $repository->getName());
        $this->assertEquals('clone url', $repository->getCloneUrl());
        $this->assertEquals('K-Phoen/test', $repository->getIdentifier());
        $this->assertEquals('K-Phoen/test', (string) $repository);
    }

    public function testItCanBeTransformedToAnArray()
    {
        $repository = new Git\Repository('K-Phoen', 'test', 'clone url');
        $data = $repository->toArray();

        $this->assertEquals('K-Phoen', $data['owner']);
        $this->assertEquals('test', $data['name']);
        $this->assertEquals('clone url', $data['clone_url']);
    }
}
