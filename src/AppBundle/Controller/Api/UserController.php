<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 20.05.2016
 * Time: 00:10
 */

namespace AppBundle\Controller\Api;

use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractApiController
{
    /**
     * @Route("/user/{id}", name="user_show")
     * @param User $user
     */
    public function showAction(User $user)
    {
        return new JsonResponse(array(
                "id" => $user->getId(),
                "login" => $user->getLogin(),
                "color" => $user->getColor(),
                "lastVisit" => $user->getNbDaysAbsent(),
                "signup" => $user->getNbDaysSignedUp(),
                "dran" => $user->getNbDran()
        ));
    }
}