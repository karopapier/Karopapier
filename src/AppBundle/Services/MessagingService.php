<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 24.01.2017
 * Time: 17:10
 */

namespace AppBundle\Services;


use AppBundle\Entity\Message;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;

class MessagingService
{
    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var MessageNormalizer
     */
    private $normalizer;
    /**
     * @var RealtimePush
     */
    private $push;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        EntityManager $em,
        MessageNormalizer $normalizer,
        RealtimePush $push,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->normalizer = $normalizer;
        $this->push = $push;
        $this->logger = $logger;
    }

    /**
     * @param User $sender
     * @param User $receiver
     * @param $text
     * @return Message
     */
    public function add(User $sender, User $receiver, $text)
    {
        $senderMessage = new Message($sender, $receiver, $text, "tx");
        $receiverMessage = new Message($receiver, $sender, $text, "rx");

        $this->em->persist($senderMessage);
        $this->em->persist($receiverMessage);
        $this->em->flush();

        return $senderMessage;
    }

}