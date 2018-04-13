<?php

namespace AppBundle\Controller;

use AppBundle\Chat\ChatService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ChatController extends Controller
{
    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/chat/realchatenter_2", name="legacy_chat_enter")
     * @Template("chat/realchatenter.html.twig")
     */
    public function realchatEnterAction(Request $request, ChatService $chatService)
    {
        $message = $request->get("chatline");
        if ($message) {
            $chatService->newMessage($this->getUser(), $message);
        }

        return array();
    }
}
