<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Game;

class GameController extends Controller
{
    /**
     * @Route("/game/{id}", name="game_show")
     */
    public function showAction(Request $request, Game $game)
    {
        // replace this example code with whatever you need
        return $this->render('game/show.html.twig', array(
                "game" => $game
        ));
    }
}
