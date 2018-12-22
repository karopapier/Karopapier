<?php

namespace AppBundle\Modules\Games\Controller;

use AppBundle\Modules\Games\DTO\GameFilterSettings;
use AppBundle\Modules\Games\Form\GameFilterType;
use AppBundle\Repository\GameRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GameListController extends AbstractController
{
    /**
     * @var GameRepository
     */
    private $repository;

    public function __construct(GameRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/games/", name="game_list")
     * @Template("Games/templates/list.html.twig")
     */
    public function listAction(Request $request)
    {
        $filter = GameFilterSettings::createFromParameters($request->query);
        $form = new GameFilterType();
        $form = $this->createForm(GameFilterType::class, $filter);
        $form->handleRequest($request);

        $games = [];
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Map[] $games */
            $qb = $this->repository->createQueryBuilder('g');
            $parameters = [];
            if ($filter->name !== '') {
                $qb->andWhere('g.name LIKE :name');
                $parameters['name'] = '%'.$filter->name.'%';
            }
            $games = $qb->getQuery()->setMaxResults(12)->execute($parameters);
        }

        return [
            'games' => $games,
            'filter_form' => $form->createView(),
        ];
    }
}
