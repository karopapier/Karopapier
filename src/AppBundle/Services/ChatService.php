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
use AppBundle\Event\ChatMessageEvent;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ChatService
{
    /** @var  string $chatlogpath */
    private $chatlogpath;

    /** @var Registry $em */
    private $em;

    /** @var EventDispatcher $dispatcher */
    private $dispatcher;

    /** @var LoggerInterface $logger */
    private $logger;

    /**
     * ChatService constructor.
     * @param $chatlogpath
     * @param Registry $registry
     * @param EventDispatcher $dispatcher
     */
    public function __construct($chatlogpath, Registry $registry, EventDispatcherInterface $dispatcher, LoggerInterface $logger)
    {
        $this->chatlogpath = $chatlogpath;
        $this->em = $registry->getManager();
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
    }

    public function getLast()
    {
        $mama = $this->em->getRepository("AppBundle:User")->find(26);
        $chatmessage = new ChatMessage($mama, "Noch nicht, aber bald");
        return $chatmessage;
    }

    /**
     * @param User $user
     * @param $message
     */
    public function newMessage(User $user, $message)
    {
        $this->checkDate();
        $cm = $this->add($user, $message);
        $this->addToLog($cm);
        $this->addToRedis($cm);
        $event = new ChatMessageEvent($cm);
        $this->dispatcher->dispatch("chat_message", $event);
        return $cm;
    }

    /**
     * @param User $user
     * @param $message
     * @return ChatMessage
     */
    public function add(User $user, $message)
    {
        $chatmessage = new ChatMessage($user, $message);
        $this->em->persist($chatmessage);
        return $chatmessage;
    }

    public function addToLog(ChatMessage $chatMessage)
    {

    }

    public function addToRedis(ChatMessage $chatMessage)
    {

    }

    public function checkDate()
    {
        $lastMessage = $this->getLast();
        $lastDay = date("Y-m-d", $lastMessage->getTs());
        $today = date('Y-m-d', time());
        if ($today != $lastDay) {
            $this->logger->debug("Date change in chat");
            $this->add(null, '---------------- ' . $today . ' ----------------');
        }
    }
}