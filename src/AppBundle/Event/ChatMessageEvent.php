<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 07.12.2016
 * Time: 20:01
 */

namespace AppBundle\Event;


use AppBundle\Entity\ChatMessage;

class ChatMessageEvent
{
    /** @var ChatMessage $chatmessage */
    private $chatmessage;

    /**
     * ChatMessageEvent constructor.
     * @param ChatMessage $chatmessage
     */
    public function __construct($chatmessage)
    {
        $this->chatmessage = $chatmessage;
    }

    /**
     * @return ChatMessage
     */
    public function getChatmessage()
    {
        return $this->chatmessage;
    }
}