<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 06.01.2019
 * Time: 00:07
 */

namespace AppBundle\Controller\Api;

use AppBundle\Repository\KarolenderBlattRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class KarolenderblattController
{
    /**
     * @var KarolenderBlattRepository
     */
    private $repository;

    public function __construct(KarolenderBlattRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/karolenderblatt/{y}-{m}-{d}", requirements={"y": "\d{4}","m": "\d{2}", "d": "\d{2}"})
     */
    public function get($y, $m, $d)
    {
        $blaetter = $this->repository->getByMonthDay($m, $d);

        $requestedDate = \DateTime::createFromFormat("Y-m-d", "$y-$m-$d");

        $blaetterData = [];
        /** @var  $blatt */
        foreach ($blaetter as $blatt) {
            $eventDate = $blatt->getEventDate();

            // Back to the future?
            if ($requestedDate < $eventDate) {
                continue;
            }

            $diffInterval = $requestedDate->diff($eventDate);
            $diffYears = $diffInterval->y;
            $line = str_replace('{DIFF}', $diffYears, $blatt->getLine());
            $line = str_replace('vor 1 Jahren', 'vor einem Jahr', $line);

            $blattData = [
                'posted' => $blatt->getPostedDate()->format('Y-m-d'),
                'line' => $line,
            ];

            $blaetterData[] = $blattData;

        }

        return new JsonResponse($blaetterData);

    }
}