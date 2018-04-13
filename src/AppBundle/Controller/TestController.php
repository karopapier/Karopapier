<?php

namespace AppBundle\Controller;

use AppBundle\Realtime\RealtimePush;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TestController extends Controller
{
    /**
     * @Route("/test", name="test")
     */
    public function indexAction(Request $request, RealtimePush $push)
    {
        $user = $this->getDoctrine()->getRepository("AppBundle:User")->findOneBy(array("login" => "Didi"));
        $push->notifyGeneric($user, "lala", []);

        return $this->render('test/index.html.twig', array());
    }
}
