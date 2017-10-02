<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 20.05.2016
 * Time: 00:10
 */

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Message;
use AppBundle\Entity\User;
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
        $user = $this->getUser();
        /** @var User $user */
        $data = $this->getJson($request);
        list($recipientId, $text) = $this->requireKeys($data, array("userId", "text"));
        $recipient = $this->getDoctrine()->getRepository("AppBundle:User")->findOneBy(["id" => $recipientId]);

        if (!$recipient) {
            return $this->sendError(404, "UNKNOWN USER");
        }

        if ($recipient->getId() == $user->getId()) {
            return $this->sendError(400, "SELF");
        }

        $message = $this->get("messaging_service")->add($user, $recipient, $text);
        $json = new JsonResponse($message->toArray());
        $json->setCallback($request->get("callback"));

        return $json;
    }
}
