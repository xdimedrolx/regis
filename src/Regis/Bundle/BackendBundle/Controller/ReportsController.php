<?php

declare(strict_types=1);

namespace Regis\Bundle\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Regis\Application\Entity;

class ReportsController extends Controller
{
    public function detailAction(Entity\Inspection\Report $report)
    {
        return $this->render('@RegisBackend/Reports/report.html.twig', [
            'report' => $report,
        ]);
    }
}