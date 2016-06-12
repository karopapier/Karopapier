<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;

class UserController extends Controller
{
    /**
     * @Route("/user/new/{id}", name="user_show")
     */
    public function showAction(Request $request, User $user)
    {
        $id = $user->getId();

        //Gefahrene Meter
        $em = $this->getDoctrine()->getManager();
        //SELECT SUM( SQRT( POW( x_vec, 2 ) + POW( y_vec, 2 ) ) * .5 )/100 from karo_moves where U_ID=1

        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT SUM( SQRT( POW( x_vec, 2 ) + POW( y_vec, 2 ) ) * .5 )/100 as driven from karo_moves where U_ID=:id");
        $statement->bindValue('id', $id);
        $statement->execute();
        $results = $statement->fetchAll();
        $distance = 0;
        if ((count($results)) > 0) {
            $distance = $results[0]["driven"];
        }

        // replace this example code with whatever you need
        return $this->render('user/show.html.twig', array(
                "user" => $user,
                "distance" => $distance
        ));
    }

}
