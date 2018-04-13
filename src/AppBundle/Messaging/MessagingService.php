<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 24.01.2017
 * Time: 17:10
 */

namespace AppBundle\Messaging;


use AppBundle\Entity\Message;
use AppBundle\Entity\User;
use AppBundle\Services\RealtimePush;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;

class MessagingService
{
    /**
     * @var ObjectManager
     */
    private $manager;
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
        ObjectManager $manager,
        MessageNormalizer $normalizer,
        RealtimePush $push,
        LoggerInterface $logger
    ) {
        $this->manager = $manager;
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

        $this->manager->persist($senderMessage);
        $this->manager->persist($receiverMessage);
        $this->manager->flush();

        $this->push->notifyGeneric($receiver, "msg", $receiverMessage->toArray());

        return $senderMessage;
    }

    public function getUnreadCounterById($id)
    {
        return $this->manager->getRepository("AppBundle:Message")->getUnreadById($id);
    }

    public function getUnreadCounter(User $user)
    {
        return $this->getUnreadCounterById($user->getId());
    }

    public function setAllRead($user, $contact)
    {
        return $this->manager->getRepository("AppBundle:Message")->setAllRead($user->getId(), $contact->getId());
    }
}