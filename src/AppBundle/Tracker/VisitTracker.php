<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 19.05.2016
 * Time: 00:40
 */

namespace AppBundle\Tracker;

use AppBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;

class VisitTracker implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ObjectManager
     */
    private $em;

    public function __construct(LoggerInterface $logger, ObjectManager $em)
    {
        $this->logger = $logger;
        $this->em = $em;
    }

    public function onSecurityAuthenticationSuccess(AuthenticationEvent $e)
    {
        $token = $e->getAuthenticationToken();
        $user = $token->getUser();

        if ($user == "anon.") {
            return;
        }

        //log the visit
        $this->logger->debug("Logging visit for ".$user->getLogin());

        //currentvisit on user
        /** @var User $user */
        $user->touch();
        $this->em->persist($user);
        $this->em->flush();

        //$query="insert into $visitstable (U_ID,visitdate) VALUES(\"$_SESSION[S_u_id]\",now())";
        $sql = "INSERT IGNORE INTO karo_visits (U_ID, visitdate) VALUES(:id, now())";
        $stmt = $this->em->getConnection()->prepare($sql);
        $stmt->bindValue(":id", $user->getId());
        $stmt->execute();
    }

    public static function getSubscribedEvents()
    {
        return [
            AuthenticationEvents::AUTHENTICATION_SUCCESS => ['onSecurityAuthenticationSuccess'],
        ];
    }
}
