<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 12.12.2016
 * Time: 08:52
 */

namespace AppBundle\Game;

use AppBundle\Entity\Game;
use AppBundle\Entity\Move;
use AppBundle\Entity\Player;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;

/**
 * Class GameChecker
 *
 * Verify  game's consistency, state, players status, start/finish, ...
 * @package AppBundle\Game
 */
class GameChecker
{
    /**
     * @var GameFinisher
     */
    private $finisher;

    /** @var EntityManager $em */
    private $em;

    /** @var LoggerInterface $logger */
    private $logger;

    public function __construct(GameFinisher $finisher, EntityManager $em, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->gr = $em->getRepository('AppBundle:Game');
        $this->ur = $em->getRepository('AppBundle:User');
        $this->pr = $em->getRepository('AppBundle:Player');
        $this->logger = $logger;
        $this->finisher = $finisher;
    }

    public function validateById($id)
    {
        $this->logger->debug("Check for Game with id ".$id);
        $game = $this->gr->find($id);
        if (!$game) {
            $this->logger->warning(
                sprintf(
                    "Game ID %s does not exist",
                    $id
                )
            );

            return null;
        }

        return $this->validate($game);
    }

    public function validate(Game $game)
    {
        $this->logger->info(sprintf("Validate Game |%s - %s|", $game->getId(), $game->getName()));

        $query = $this->em->createQuery(
            'SELECT g,p,u FROM AppBundle:Game g JOIN g.players p JOIN p.user u WHERE g.id = :gid'
        );
        $query->setParameter("gid", $game->getId());
        /** @var Game $game */
        $game = $query->getSingleResult();

        $allStarted = true;
        $allFinished = true;
        $oneActive = false;
        $players = $game->getPlayers();
        /** @var Player $player */
        foreach ($players as $player) {
            $this->logger->info("Check Player ".$player);
            $f = $player->isFinished();
            $active = $player->isActive();
            $this->logger->info(sprintf("Fin %s Stat %s", $player->getFinished(), $player->getStatus()));
            if ($active) {
                $this->logger->info($player." is active");
                $oneActive = true;
            }
            if (!$f) {
                $this->logger->info($player." not finished ".$player->getFinished());
                $allFinished = false;
            }
        }

        if (!$oneActive) {
            $this->logger->info("All players inactive");
            if (!$game->isFinished()) {
                $this->logger->info("Game was not finished, so I finish it");
                $this->ensureFinished($game);
            }
        }

        return $game;
    }

    private function ensureAllPlayersFinished(Game $game)
    {

    }

    public function ensureFinished(Game $game)
    {
        /** @var Move $lm */
        $fd = $game->getFinishedDate();
        if (!$fd) {
            try {
                $lm = $this->em->getRepository("AppBundle:Move")->findLastMove($game);
                $fd = $lm->getDate();
            } catch (\Exception $exception) {
                $fd = new \DateTime("now");
            }
        }
        $this->finisher->finish($game, $fd);
        $this->em->persist($game);
        $this->em->flush();
    }


}