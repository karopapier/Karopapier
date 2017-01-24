<?php

namespace AppBundle\Controller;

use AppBundle\Form\GameType;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Game;

class GameController extends Controller
{
    /**
     * @Route("/game/newnew", name="game_new")
     * @Security("has_role('ROLE_USER')")
     * @Template("default/new")
     */
    public function newAction(Request $request)
    {
        $game = new Game();
        $game->setName("New Aera");

        // create a task and give it some dummy data for this example
        $form = $this->createForm('AppBundle\Form\GameType', $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // ... perform some action, such as saving the task to the database

            return $this->redirectToRoute('task_success');
        }

        return array(
                'form' => $form->createView(),
        );
    }

    /**
     * @Route("/game/{id}", requirements={"id" = "\d+"}, name="game_show")
     * @Template("game/show.html.twig")
     */
    public function showAction(Request $request, Game $game)
    {
        $gl = $this->get("game_loader");
        $gl->load($game);

        // replace this example code with whatever you need
        return array(
                "game" => $game
        );
    }

}
