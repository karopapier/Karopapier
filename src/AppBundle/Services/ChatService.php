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
use Snc\RedisBundle\Client\Phpredis\Client;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ChatService
{
    /** @var  string $chatlogpath */
    private $chatlogpath;
    /** @var  string $chatRedisKey */
    private $chatRedisKey;

    /** @var Registry $em */
    private $em;

    /** @var EventDispatcher $dispatcher */
    private $dispatcher;

    /** @var LoggerInterface $logger */
    private $logger;

    /** @var Client $redis */
    private $redis;

    /**
     * ChatService constructor.
     * @param $chatlogpath
     * @param Registry $registry
     * @param EventDispatcher $dispatcher
     */
    public function __construct($config, Registry $registry, EventDispatcherInterface $dispatcher, Client $redis, LoggerInterface $logger)
    {
        $this->chatlogpath = $config["logpath"];
        $this->chatRedisKey = $config["redis_key"];
        $this->em = $registry->getManager();
        $this->dispatcher = $dispatcher;
        $this->redis = $redis;
        $this->logger = $logger;
    }

    public function getLast()
    {
        return new ChatMessage(new User(), "Bald");
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
        //Karo2 uses only user (login string), text (actual text) and time (hh:mm string)
        $data = array(
                "user" => $chatMessage->getLogin(),
                "time" => $chatMessage->getTime(),
                "text" => $chatMessage->getText()
        );
        $this->redis->rpush($this->chatRedisKey, json_encode($data));
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