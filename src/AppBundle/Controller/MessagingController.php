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
class MessagingController extends Controller
{
    /**
     * @param $name
     * @Route("/zettel/{name}", name="messaging")
     * @Template("app.html.twig")
     */
    public function indexAction($name = "")
    {
        $users = $this->getDoctrine()->getRepository("AppBundle:User")->getActiveUsers();
        $data = [];
        foreach ($users as $user) {
            $data[] = $user->toArray();
        }

        return [
            "users" => $data,
        ];
    }

    /**
     * @Route("/mitteilungen", name="messaging_redirect")
     */
    public function redirAction()
    {
        return $this->redirect("/zettel");
    }
}
