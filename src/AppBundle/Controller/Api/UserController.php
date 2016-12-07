<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 20.05.2016
 * Time: 00:10
 */

namespace AppBundle\Controller\Api;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractApiController
{
    /**
     * @Route("/users/{id}", name="api_users_show", requirements={"id": "\d+"})
     * @Route("/user/{id}", name="api_user_show", requirements={"id": "\d+"})
     * @param User $user
     */
    public function showAction(Request $request, User $user)
    {
        $json = new JsonResponse($user->toArray());
        $json->setCallback($request->get("callback"));
        return $json;
    }

    /**
     * @Route("/login", name="api_user_login")
     * @Method("POST")
     */
    public function loginAction(Request $request)
    {
        $login = "";
        $password = "";
        $json = $request->getContent();
        $obj = json_decode($json, true);
        if (isset($obj["login"]) && isset($obj["password"])) {
            $login = $obj["login"];
            $password = $obj["password"];
        } else {
            throw new AccessDeniedHttpException();
        }

        $em = $this->get("doctrine.orm.default_entity_manager");
        $user = $em->getRepository('AppBundle:User')->findOneBy(array(
                "login" => $login
        ));
        if (!$user) {
            throw new AccessDeniedHttpException();
        }

        if ($password != $user->getPassword()) {
            throw new AccessDeniedHttpException();
        }

        $json = new JsonResponse($user->toArray());
        $json->headers->setCookie($this->get("legacy_cookie_setter")->getCookie($user->getId(), $password));
        $json->setCallback($request->get("callback"));
        return $json;
    }

    /**
     * @Route("/users/{login}", name="api_users_show_slug")
     * @Route("/user/{login}", name="api_user_show_slug")
     * @param User $user
     */
    public function showSlugAction(Request $request, User $user)
    {
        $json = new JsonResponse($user->toArray());
        $json->setCallback($request->get("callback"));
        return $json;
    }

}
