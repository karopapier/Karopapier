<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 20.05.2016
 * Time: 20:50.
 */

namespace AppBundle\Realtime;

use AppBundle\Entity\ChatMessage;
use AppBundle\Entity\User;
use AppBundle\Event\ChatMessageEvent;
use AppBundle\Event\KaroEvents;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Turted\TurtedBundle\Service\TurtedRestPushService;

class RealtimePush implements EventSubscriberInterface
{
    private $turtedPush;
    private $logger;

    public function __construct(
        TurtedRestPushService $turtedPush,
        LoggerInterface $logger
    ) {
        $this->turtedPush = $turtedPush;
        $this->logger = $logger;
    }

    /**
     * @param User $user
     * @param $event
     * @param $payload
     */
    private function notifyUser(User $user, $event, $payload)
    {
        $username = $user->getUsername();

        $this->turtedPush->notifyUser(
            $username,
            $event,
            $payload
        );
    }

    public function notifyChatMessage(ChatMessage $cm)
    {
        $this->turtedPush->notifyChannel(
            "karochat",
            "CHAT:MESSAGE",
            $cm->toApi()
        );
    }

    public function notifyGeneric(User $user, $eventName, $data)
    {
        $this->notifyUser(
            $user,
            $eventName,
            $data
        );
    }

    public function onChatMessage(ChatMessageEvent $chatMessageEvent)
    {
        $cm = $chatMessageEvent->getChatmessage();
        $this->notifyChatMessage($cm);
    }

    public static function getSubscribedEvents()
    {
        return [
            KaroEvents::CHAT_MESSAGE => [
                'onChatMessage',
            ],
        ];
    }
}
