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
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

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

    /**
     * @Route("/user/login", name="api_user_login")
     * @Method("POST")
     */
    public function loginAction($username, $password)
    {
        $json = $request->getContent();
        $obj = json_decode($json, true);
        if (isset($obj["login"]) && isset($obj["password"])) {
            $login = $obj["login"];
            $password = $obj["password"];
        } else {
            throw new AccessDeniedHttpException();
        }
    }
}
