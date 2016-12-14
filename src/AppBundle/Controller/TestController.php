<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TestController extends Controller
{
    /**
     * @Route("/test", name="test")
     */
    public function indexAction(Request $request)
    {
        $finder = $this->get("game_inconsistency_finder");
        $finder->checkFinishedWithoutKaroMAMA();
        $finder->checkStartedWithoutPlayers();
        return $this->render('test/index.html.twig', array());
    }
}
