<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 26.11.2016
 * Time: 16:41
 */

namespace AppBundle\Chat;

use AppBundle\Entity\ChatMessage;
use AppBundle\Entity\User;
use AppBundle\Event\ChatMessageEvent;
use AppBundle\Services\ConfigService;
use AppBundle\Services\LegacyChatlineConverter;
use AppBundle\Services\MessageNormalizer;
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
    public function __construct(
        ConfigService $config,
        Registry $registry,
        MessageNormalizer $messageNormalizer,
        LegacyChatlineConverter $legacyChatlineConverter,
        EventDispatcherInterface $dispatcher,
        Client $redis,
        LoggerInterface $logger
    ) {
        $this->chatlogpath = $config->get('chat.logpath');
        $this->chatRedisKey = $config->get('chat.redis_key');
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
     * To be used to FILL the collection internally, not on new User Messages. See newMessage instead
     * @param User|null $user
     * @param $message
     * @param int $lineId
     * @param string $legacyLine
     * @param string $time
     * @return ChatMessage
     */
    public function add(User $user = null, $message, $lineId = 0, $legacyLine = "", $time = "00:00")
    {
        $text = $this->normalizer->normalize($message);

        if ($lineId == 0) {
            $lastId = $this->getLast()->getLineId();
            $lineId = $lastId + 1;
        }
        $chatmessage = new ChatMessage($user, $message, $lineId, $legacyLine, $time);
        $this->em->persist($chatmessage);

        return $chatmessage;
    }

    private function addToLog(ChatMessage $chatMessage)
    {
        $fh = fopen($this->chatlogpath, "a");
        if (!$fh) {
            throw new \Exception("Chatlog not writable");
        }
        fwrite($fh, $chatMessage->getLegacyLine());
        fclose($fh);
    }

    private function addToRedis(ChatMessage $chatMessage)
    {
        //Karo2 uses only user (login string), text (actual text) and time (hh:mm string)
        $data = array(
            "user" => $chatMessage->getLogin(),
            "time" => $chatMessage->getTime(),
            "text" => $chatMessage->getText(),
        );
        $this->redis->rpush($this->chatRedisKey, json_encode($data));
    }

    public function checkDate()
    {
        $lastMessage = $this->getLast();
        if (!$lastMessage) {
            return;
        }
        $lts = $lastMessage->getTs();
        if (!$lts) {
            return;
        }
        $lastDay = $lts->format("Y-m-d");
        $today = date('Y-m-d', time());
        if ($today != $lastDay) {
            $this->logger->debug("Date change in chat");
            $line = '---------------- '.$today.' ----------------';
            $this->add(null, $line, 0, $line);
        }
    }
}
