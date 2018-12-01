<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class MessagingController
 * @Security("has_role('ROLE_USER')")
 * @package AppBundle\Controller
 */
class AppController extends Controller
{
    /**
     * @param $name
     * @Route("/zettel/{data}", name="app_messaging")
     * @Route("/spiele/{data}", name="app_game")
     * @Route("/dran2", name="app_dran")
     * @Route("/erstellen", name="app_newgame")
     * @Route("/chat3", name="app_chat")
     * @Template("app.html.twig")
     */
    public function indexAction($data = "")
    {
        return [];
    }

    /**
     * @Route("/mitteilungen", name="messaging_redirect")
     */
    public function redirAction()
    {
        return $this->redirect("/zettel");
    }
}
