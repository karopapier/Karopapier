<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 19.05.2016
 * Time: 00:40
 */

namespace AppBundle\Services;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;

class VisitLogService
{
    private $em;

    public function __construct(\Psr\Log\LoggerInterface $logger, EntityManager $em)
    {
        $this->em = $em;
        $this->logger = $logger;
    }

    public function onSecurityAuthenticationSuccess(AuthenticationEvent $e)
    {
        $token = $e->getAuthenticationToken();
        $user = $token->getUser();

        if ($user == "anon.") return;

        //log the visit
        $this->logger->debug("Logging visit for " . $user->getLogin());

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
}
