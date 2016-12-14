<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 12.12.2016
 * Time: 08:52
 */

namespace AppBundle\Services;


use AppBundle\Entity\Game;
use AppBundle\Event\GameEvent;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class GameChecker
 *
 * Verify  game's consistency, state, players status, start/finish, ...
 * @package AppBundle\Services
 */
class GameFinisher
{

    /** @var EntityManager */
    private $em;

    /** @var LoggerInterface */
    private $logger;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(EntityManager $em, LoggerInterface $logger, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->ur = $em->getRepository('AppBundle:User');
        $this->logger = $logger;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Mark the game as finished, with optional timestamp
     * @param Game $game
     * @param \DateTimeInterface $fd
     */
    public function finish(Game $game, \DateTimeInterface $fd = null)
    {
        $this->logger->info("Finishing Game " . $game->getId());
        if ($fd === null) {
            $this->finisheddate = new \DateTime("now");
        } else {
            $this->finisheddate = $fd;
        }
        $game->finish($fd, $this->em->getReference('AppBundle:User', 26)); //KaroMAMA
        $event = new GameEvent($game);
        $this->dispatcher->dispatch("game.finished", $event);
        $this->em->persist($game);
    }
}