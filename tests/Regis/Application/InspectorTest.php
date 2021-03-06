<?php

namespace Tests\Regis\Application;

use Regis\Application\Inspection;
use Regis\Application\Inspector;
use Regis\Domain\Entity\Inspection\Violation;
use Regis\Domain\Model;
use Regis\Infrastructure\Vcs;

class InspectorTest extends \PHPUnit_Framework_TestCase
{
    private $git;
    private $repository;
    private $gitRepository;
    private $diff;
    private $revisions;

    public function setUp()
    {
        $this->git = $this->getMockBuilder(Vcs\Git::class)->disableOriginalConstructor()->getMock();
        $this->gitRepository = $this->getMockBuilder(Vcs\Repository::class)->disableOriginalConstructor()->getMock();

        $this->diff = $this->getMockBuilder(Model\Git\Diff::class)->disableOriginalConstructor()->getMock();
        $this->repository = $this->getMockBuilder(Model\Git\Repository::class)->disableOriginalConstructor()->getMock();
        $this->revisions = $this->getMockBuilder(Model\Git\Revisions::class)->disableOriginalConstructor()->getMock();

        $this->git->expects($this->any())
            ->method('getRepository')
            ->with($this->repository)
            ->will($this->returnValue($this->gitRepository));
        $this->gitRepository->expects($this->any())
            ->method('getDiff')
            ->with($this->revisions)
            ->will($this->returnValue($this->diff));
    }

    public function testItStartsByUpdatingTheGitRepo()
    {
        $inspector = new Inspector($this->git);

        $this->gitRepository->expects($this->once())->method('update');

        $inspector->inspect($this->repository, $this->revisions);
    }

    public function testItGivesTheDiffToTheInspectionsAndReturnsAUnifedReport()
    {
        $inspection1 = $this->getMockBuilder(Inspection::class)->getMock();
        $inspection2 = $this->getMockBuilder(Inspection::class)->getMock();

        $violation1 = $this->getMockBuilder(Violation::class)->disableOriginalConstructor()->getMock();
        $violation2 = $this->getMockBuilder(Violation::class)->disableOriginalConstructor()->getMock();

        $inspection1->expects($this->once())
            ->method('inspectDiff')
            ->with($this->diff)
            ->will($this->returnValue(new \ArrayIterator([$violation1])));

        $inspection2->expects($this->once())
            ->method('inspectDiff')
            ->with($this->diff)
            ->will($this->returnValue(new \ArrayIterator([$violation2])));

        $inspector = new Inspector($this->git, [$inspection1, $inspection2]);
        $report = $inspector->inspect($this->repository, $this->revisions);

        $this->assertCount(2, iterator_to_array($report->getViolations()));
    }
}
