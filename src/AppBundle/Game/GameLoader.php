<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 01.06.2016
 * Time: 22:27
 */

namespace AppBundle\Game;


use AppBundle\Entity\Game;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;

class GameLoader
{

    /**
     * @var ObjectManager
     */
    private $em;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ObjectManager $em, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
    }

    public function load(Game $game)
    {
        //load all relations in one query?!
        $query = $this->em->createQuery(
            'SELECT g, p, u FROM AppBundle\Entity\Game g JOIN g.players p  JOIN p.user u WHERE g.id = :gid'
        )->setParameter('gid', $game->getId());
        $query->execute();
    }

    public function loadWithMoves(Game $game)
    {
        //load all relations in one query?!
        $query = $this->em->createQuery(
            'SELECT g, p, u,m FROM AppBundle\Entity\Game g JOIN g.players p  JOIN p.user u JOIN p.moves m WHERE g.id = :gid'
        )->setParameter('gid', $game->getId());
        $query->execute();
    }
}