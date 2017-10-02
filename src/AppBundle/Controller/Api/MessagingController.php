<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 20.05.2016
 * Time: 00:10
 */

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Message;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @Security("has_role('ROLE_USER')")
 * @package AppBundle\Controller\Api
 */
class MessagingController extends AbstractApiController
{
    /**
     * @Route("/messages", name="api_messages_list")
     * @Method("GET")
     */
    public function listAction(Request $request)
    {
        $user = $this->getUser();
        $messages = $this->getDoctrine()->getRepository("AppBundle:Message")->getMessages($user);
        $data = [];
        foreach ($messages as $message) {
            /** @var Message $message */
            $data[] = $message->toArray();
        }
        $json = new JsonResponse($data);
        $json->setCallback($request->get("callback"));

        return $json;
    }

    /**
     * @Route("/messages", name="api_message_add")
     * @Method("POST")
     */
    public function addAction(Request $request)
    {
        $data = $this->getJson($request);
        list($userId, $text) = $this->requireKeys($data, array("userId", "text"));
        $recipient = $this->getDoctrine()->getRepository("AppBundle:User")->findOneBy(["id" => $userId]);
        if (!$recipient) {
            return $this->sendError(404, "UNKNOWN USER");
        }
        $json = new JsonResponse(
            [
                "id" => md5(time()),
                "text" => $text,
                "user_name" => $recipient->getLogin(),
            ]
        );
        $json->setCallback($request->get("callback"));

        return $json;
    }
}
