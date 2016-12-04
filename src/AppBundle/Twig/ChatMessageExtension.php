<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 18.07.2016
 * Time: 23:57
 */

namespace AppBundle\Twig;


use AppBundle\Services\ChatService;

class ChatMessageExtension extends \Twig_Extension
{
    /** @var ChatService $cs */
    private $cs;

    public function __construct(ChatService $chatService)
    {
        $this->cs = $chatService;
    }

    public function getLastMessage()
    {
        return $this->cs->getLast();
    }

    public function getFunctions()
    {
        return array(
                'lastchatmessage' => new \Twig_SimpleFunction("lastchatmessage", array($this, "getLastMessage"))
        );
    }

    public function getName()
    {
        return "chat_message_extension";
    }
}