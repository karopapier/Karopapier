<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 20.05.2016
 * Time: 00:10
 */

namespace AppBundle\Controller\Api;

use AppBundle\Entity\User;
use AppBundle\Messaging\MessagingService;
use AppBundle\Repository\UserRepository;
use AppBundle\Security\LegacyCookieSetter;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

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
     * @Route("/users/check", name="api_users_check")
     * @Route("/user/check", name="api_user_check")
     * @Route("/user/check.json", name="api_user_check_json")
     * @param User $user
     */
    public function checkAction(
        Request $request,
        MessagingService $messagingService,
        AuthorizationCheckerInterface $checker
    ) {
        if (!$checker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(["ERROR" => "LOGIN_REQUIRED"], 401);
        }

        /** @var User $user */
        $user = $this->getUser();
        $data = $user->toArray();
        $data["uc"] = $messagingService->getUnreadCounter($user);
        $json = new JsonResponse($data);
        $json->setCallback($request->get("callback"));

        return $json;

    }

    /**
     * @Route("/users", name="api_users_list", requirements={"id": "\d+"})
     */
    public function listAction(Request $request)
    {
        $loginFilter = $request->get("login", "");
        $users = $this->getDoctrine()->getRepository("AppBundle:User")->getActiveUsers($loginFilter);
        $data = [];
        foreach ($users as $user) {
            $data[] = $user->toArray();
        }
        $json = new JsonResponse($data);
        $json->setCallback($request->get("callback"));

        return $json;
    }

    /**
     * @Route("/login", name="api_user_login")
     * @Method("POST")
     */
    public function loginAction(Request $request, ObjectManager $em, LegacyCookieSetter $cookieSetter)
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

        $user = $em->getRepository('AppBundle:User')->findOneBy(
            array(
                "login" => $login,
            )
        );
        if (!$user) {
            throw new AccessDeniedHttpException();
        }

        if ($password != $user->getPassword()) {
            throw new AccessDeniedHttpException();
        }

        $json = new JsonResponse($user->toArray());
        $json->headers->setCookie($cookieSetter->getCookie($user->getId(), $password));
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

    /**
     * @Route("/users/{id}/dran", name="api_users_dran")
     * @Route("/user/{id}/dran", name="api_user_dran")
     * @param User $user
     */
    public function showDranGames(Request $request, User $user, ObjectManager $em)
    {
        $gameRepository = $em->getRepository('AppBundle:Game');
        $games = $gameRepository->getDranGames($user);

        $json = new JsonResponse($games);
        $json->setCallback($request->get("callback"));

        return $json;
    }

    /**
     * @Route("/blockers", name="api_blockers")
     * @param Request $request
     * @param UserRepository $repository
     */
    public function blockerlist(Request $request, UserRepository $repository)
    {
        $data = [];
        $users = $repository->getBlockerList();
        foreach ($users as $user) {
            $u = array(
                'id' => $user->getId(),
                'login' => $user->getLogin(),
                'dran' => $user->getNbDran(),
                'activeGames' => $user->getNbGames(),
            );
            $data[] = $u;
        }

        $response = new JsonResponse($data);
        $response->setCallback($request->get('callback'));

        return $response;
    }

    /**
     * @Route("/users/{uid}/blockers", name="api_user_blockers")
     * @param Request $request
     * @param UserRepository $repository
     */
    public function userBlockerlist(Request $request, $uid, UserRepository $userRepository)
    {
        $user = $userRepository->find($uid);
        if (!$user) {
            throw new NotFoundHttpException();
        }

        $data = $userRepository->getUserBlockerData($user);

        $response = new JsonResponse($data);
        $response->setCallback($request->get('callback'));

        return $response;
    }
}
