<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Game;
use AppBundle\Game\GameLoader;
use AppBundle\Module\GameCreation\Form\GameType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class GameController extends Controller

{
    /**
     * @Route("/game/newnew", name="game_new")
     * @Security("has_role('ROLE_USER')")
     * @Template("game/new.html.twig")
     */
    public function newAction(Request $request)
    {
        $game = new Game();
        $game->setName("New Aera");

        // create a task and give it some dummy data for this example
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // ... perform some action, such as saving the task to the database
            $this->getDoctrine()->getManager()->persist($game);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('game_show', ['id' => $game->getId()]);
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/game/{id}", requirements={"id" = "\d+"}, name="game_show")
     * @Template("game/show.html.twig")
     */
    public function showAction(Request $request, Game $game, GameLoader $gl)
    {
        $gl->load($game);

        // replace this example code with whatever you need
        return array(
            "game" => $game,
        );
    }

}
