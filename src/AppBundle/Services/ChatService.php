<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 26.11.2016
 * Time: 16:41
 */

namespace AppBundle\Services;


use AppBundle\Entity\ChatMessage;
use AppBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Registry;

class ChatService
{
    public function __construct($chatlogpath, Registry $registry)
    {
        $this->em = $registry->getManager();
        $this->chatlogpath = $chatlogpath;
    }

    public function getLast()
    {

    }

    /**
     * @param User $user
     * @param $message
     * @return ChatMessage
     */
    public function add(User $user, $message)
    {
        $chatmessage = new ChatMessage();
        return $chatmessage;
    }
}