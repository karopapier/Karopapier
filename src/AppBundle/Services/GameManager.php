<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 01.06.2016
 * Time: 22:27
 */

namespace AppBundle\Services;


use AppBundle\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Psr\Log\LoggerInterface;

class GameManager
{

    public function __construct(Registry $doctrine, LoggerInterface $logger)
    {
        $this->em = $doctrine->getManager();
        $this->logger = $logger;
    }

    public function load(Game $game)
    {
        //load all relations in one query?!
        $query = $this->em->createQuery('SELECT g, p, u FROM AppBundle\Entity\Game g JOIN g.players p  JOIN p.user u WHERE g.id = :gid')->setParameter('gid', $game->getId());
        $query->execute();
    }

    public function loadWithMoves(Game $game)
    {
        //load all relations in one query?!
        $query = $this->em->createQuery('SELECT g, p, u,m FROM AppBundle\Entity\Game g JOIN g.players p  JOIN p.user u JOIN p.moves m WHERE g.id = :gid')->setParameter('gid', $game->getId());
        $query->execute();
    }
}