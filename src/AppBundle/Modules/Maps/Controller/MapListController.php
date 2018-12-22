<?php

namespace AppBundle\Modules\Maps\Controller;

use AppBundle\Entity\Map;
use AppBundle\Modules\Maps\DTO\MapFilterSettings;
use AppBundle\Modules\Maps\Form\MapFilterType;
use AppBundle\Repository\MapRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MapListController extends AbstractController
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
     * @Template("Maps/templates/maplist.html.twig")
     */
    public function listAction(Request $request)
    {
        $mapFilter = MapFilterSettings::createFromParameters($request->query);
        $form = new MapFilterType();
        $form = $this->createForm(MapFilterType::class, $mapFilter);
        $form->handleRequest($request);

        $maps = [];
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Map[] $maps */
            $qb = $this->repository->getActiveMapsQueryBuilder();
            $parameters = [];
            if ($mapFilter->name !== '') {
                $qb->andWhere('m.name LIKE :name');
                $parameters['name'] = '%'.$mapFilter->name.'%';
            }
            if ($mapFilter->author !== '') {
                $qb->andWhere('m.author LIKE :author');
                $parameters['author'] = '%'.$mapFilter->author.'%';
            }
            $maps = $qb->getQuery()->execute($parameters);
        }

        return [
            'maps' => $maps,
            'filter_form' => $form->createView(),
        ];
    }
}
