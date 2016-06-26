<?php

namespace Tests\Regis\Application\CommandHandler\Github\Inspection;

use Regis\Application\ReportSummary;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;

use Regis\Application\Command;
use Regis\Application\CommandHandler;
use Regis\Application\Entity;
use Regis\Application\Event;
use Regis\Application\Inspector;
use Regis\Application\Model;
use Regis\Application\Repository;

class RunTest extends \PHPUnit_Framework_TestCase
{
    private $dispatcher;
    private $inspector;
    private $inspection;
    private $pullRequest;
    private $inspectionsRepo;
    /** @var CommandHandler\Github\Inspection\Run */
    private $handler;

    public function setUp()
    {
        $this->dispatcher = $this->getMockBuilder(EventDispatcher::class)->getMock();
        $this->inspector = $this->getMockBuilder(Inspector::class)->disableOriginalConstructor()->getMock();
        $this->inspectionsRepo = $this->getMockBuilder(Repository\Inspections::class)->getMock();

        $this->inspection = $this->getMockBuilder(Entity\Github\PullRequestInspection::class)->disableOriginalConstructor()->getMock();
        $this->pullRequest = $this->getMockBuilder(Model\Github\PullRequest::class)->disableOriginalConstructor()->getMock();

        $this->handler = new CommandHandler\Github\Inspection\Run($this->dispatcher, $this->inspector, $this->inspectionsRepo);
    }

    public function testWhenTheInspectionSucessfullyFinishes()
    {
        $reportSummary = new ReportSummary();

        $this->inspector->expects($this->once())
            ->method('inspect')
            ->with($this->pullRequest)
            ->will($this->returnValue($reportSummary));

        $this->inspectionsRepo->expects($this->exactly(2))
            ->method('save')
            ->with($this->inspection);

        $this->inspection->expects($this->once())->method('start');
        $this->inspection->expects($this->once())->method('finish');

        $this->dispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [Event::INSPECTION_STARTED, $this->anything()],
                [Event::INSPECTION_FINISHED, $this->anything()]
            );

        $this->handler->handle(new Command\Github\Inspection\Run($this->inspection, $this->pullRequest));
    }

    /**
     * @expectedException RuntimeException
     */
    public function testWhenTheInspectionFails()
    {
        $this->inspector->expects($this->once())
            ->method('inspect')
            ->with($this->pullRequest)
            ->will($this->throwException(new \RuntimeException('onoes')));

        $this->inspectionsRepo->expects($this->exactly(2))
            ->method('save')
            ->with($this->inspection);

        $this->inspection->expects($this->once())->method('start');
        $this->inspection->expects($this->once())->method('fail');

        $this->dispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [Event::INSPECTION_STARTED, $this->anything()],
                [Event::INSPECTION_FAILED, $this->anything()]
            );

        $this->handler->handle(new Command\Github\Inspection\Run($this->inspection, $this->pullRequest));
    }
}