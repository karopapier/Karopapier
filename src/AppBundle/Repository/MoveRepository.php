<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 12.12.2016
 * Time: 10:48
 */

namespace AppBundle\Repository;


use AppBundle\Entity\Game;
use AppBundle\Entity\Move;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class MoveRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Move::class);
    }

    public function findLastMove(Game $game)
    {
        $query = $this->getEntityManager()
            ->createQuery('SELECT m FROM AppBundle:Move m where m.game = :gid ORDER BY m.id DESC');
        $query->setParameters(
            array("gid" => $game->getId())
        );
        $query->setMaxResults(1);
        /** @var Move $move */
        $move = $query->getSingleResult();

        return $move;
    }
}