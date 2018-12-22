<?php

namespace AppBundle\Modules\Map\Controller;

use AppBundle\Entity\Map;
use AppBundle\Modules\Map\DTO\MapFilter;
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
     * @Template("Map/templates/maplist.html.twig")
     */
    public function listAction(Request $request)
    {
        $mapFilter = MapFilter::createFromParameters($request->query);

        /** @var Map[] $maps */
        $qb = $this->repository->getActiveMapsQueryBuilder();
        $parameters = [];
        if ($mapFilter->name !== '') {
            $qb->andWhere('m.name LIKE :name');
            $parameters['name'] = '%'.$mapFilter->name.'%';
        }
        $maps = $qb->getQuery()->execute($parameters);

        return [
            'maps' => $maps,
        ];
    }
}
