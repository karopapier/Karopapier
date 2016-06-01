<?php

namespace AppBundle\Controller;

use AppBundle\Form\GameType;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Game;

class GameController extends Controller
{
    /**
     * @Route("/game/new", name="game_new")
     * @Security("has_role('ROLE_USER')")
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

        return $this->render('default/new.html.twig', array(
                'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/game/{id}", name="game_show")
     */
    public function showAction(Request $request, Game $game)
    {
        $gm = $this->get("game_manager");
        $gm->load($game);

        // replace this example code with whatever you need
        return $this->render('game/show.html.twig', array(
                "game" => $game
        ));
    }

}
