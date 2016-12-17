<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/users/{id}", name="user_show")
     */
    public function showAction(Request $request, User $user)
    {
        $id = $user->getId();
        $moveStats = $this->get("snc_redis.default")->hGetAll("users:" . $id . ":moves");
        $months = array();
        $moveCounts = array();
        foreach ($moveStats as $month => $moveCount) {
            $months[] = $month;
            $moveCounts[] = (int)$moveCount;
        }

        return $this->render('user/show.html.twig', array(
                "user" => $user,
                "distance" => $user->getDistance(),
                "months" => $months,
                "moveCounts" => $moveCounts
        ));
    }

    /**
     * #@Route("/users.php")
     * @Route("/users/")
     * @Security("has_role('ROLE_USER')")
     */
    public function listAction(Request $request)
    {
        $um = $this->get('doctrine')->getRepository('AppBundle:User');
        $users = $um->findBy(array("active" => true), array("login" => 'ASC'));

        return $this->render('user/list.html.twig', array(
                "users" => $users,
        ));

    }

    /**
     * @Route("/showgames.php?spielevon={id}")
     * @Route("/users/{id}/games", name="user_games_list")
     */
    public function listGamesAction(Request $request, User $user)
    {

    }
}
