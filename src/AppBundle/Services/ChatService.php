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

    /** @var  MessageNormalizer */
    private $normalizer;

    /** @var  LegacyChatlineConverter */
    private $legacyChatlineConverter;

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
    public function __construct($config, Registry $registry, MessageNormalizer $messageNormalizer, LegacyChatlineConverter $legacyChatlineConverter, EventDispatcherInterface $dispatcher, $redis, LoggerInterface $logger)
    {
        $this->chatlogpath = $config["logpath"];
        $this->chatRedisKey = $config["redis_key"];
        $this->em = $registry->getManager();
        $this->repo = $this->em->getRepository("AppBundle:ChatMessage");
        $this->normalizer = $messageNormalizer;
        $this->legacyChatlineConverter = $legacyChatlineConverter;
        $this->dispatcher = $dispatcher;
        $this->redis = $redis;
        $this->logger = $logger;
    }

    public function getLast()
    {
        return $this->repo->findLast();
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
     * To be used to FILL the collection internally, not on new Messages. See newMessage instead
     * @param User|null $user
     * @param $message
     * @return ChatMessage
     */
    public function add(User $user = null, $message)
    {
        $text = $this->normalizer->normalize($message);
        $lastId = $this->getLast()->getLineId();
        $chatmessage = new ChatMessage($user, $message, $lastId + 1);
        $this->em->persist($chatmessage);
        $this->em->flush();
        return $chatmessage;
    }

    private function addToLog(ChatMessage $chatMessage)
    {
        $line = $this->legacyChatlineConverter->toLegacyChatline($chatMessage);
        $fh = fopen($this->chatlogpath, "a");
        if (!$fh) {
            throw new \Exception("Chatlog not writable");
        }
        fwrite($fh, $line);
        fclose($fh);
    }

    private function addToRedis(ChatMessage $chatMessage)
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
        if (!$lastMessage) return;
        $lastDay = $lastMessage->getTs()->format("Y-m-d");
        $today = date('Y-m-d', time());
        if ($today != $lastDay) {
            $this->logger->debug("Date change in chat");
            $this->add(null, '---------------- ' . $today . ' ----------------');
        }
    }
}
