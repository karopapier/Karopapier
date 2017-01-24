<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ChatController extends Controller
{
    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/chat/realchatenter", name="legacy_chat_enter")
     * @Template("chat/realchatenter.html.twig")
     */
    public function realchatEnterAction(Request $request)
    {
        $message = $request->get("chatline");
        if ($message) {
            $chatService = $this->get("chat_service");
            $chatService->newMessage($this->getUser(), $message);
        }
        return array();
    }
}
