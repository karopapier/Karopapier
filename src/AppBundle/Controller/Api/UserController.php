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
     * @Route("/user/{id}", name="api_user_show", requirements={"id": "\d+"})
     * @param User $user
     */
    public function showAction(User $user)
    {
        return new JsonResponse($user->toArray());
    }

    /**
     * @Route("/user/{login}", name="api_user_show_slug")
     * @param User $user
     */
    public function showSlugAction(User $user)
    {
        return new JsonResponse($user->toArray());
    }
}
