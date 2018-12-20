<?php

namespace AppBundle\Module\Map\Controller;

use AppBundle\Entity\Map;
use AppBundle\Repository\MapRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MapListController
{
    /**
     * @var MapRepository
     */
    private $repository;

    public function __construct(MapRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/maps/", name="map_list")
     * @Template("map/maplist.html.twig")
     */
    public function listAction(Request $request)
    {
        /** @var Map[] $maps */
        $qb = $this->repository->getActiveMapsQueryBuilder();
        $maps = $qb->getQuery()->execute();

        return [
            'maps' => $maps,
        ];
    }
}
