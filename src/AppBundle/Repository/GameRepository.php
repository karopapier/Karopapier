<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 12.12.2016
 * Time: 10:48
 */

namespace AppBundle\Repository;


use AppBundle\Entity\User;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use PDO;

class GameRepository extends EntityRepository
{
    public function findAllOrderedByName()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT p FROM AppBundle:Game p ORDER BY p.name ASC'
            )
            ->getResult();
    }

    public function getDranGames(User $user)
    {
        $connection = $this->getEntityManager()->getConnection();
        $qb = $connection->createQueryBuilder();
        $qb->select('G_ID as id, name, datediff(now(),datemailsent) blocked FROM karo_games');
        $qb->where('U_ID='.$user->getId());
        $qb->orderBy('datemailsent', 'desc');
        $this->isActive($qb);
        $qb->setParameter('user', $user);

        $stmt = $qb->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isActive(QueryBuilder $qb)
    {
        return $this->isStarted($this->isNotFinished($qb));
    }

    public function isFinished()
    {

    }

    /**
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    public function isStarted(QueryBuilder $qb)
    {
        $qb->andWhere('started=1');

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    public function isNotFinished(QueryBuilder $qb)
    {
        $qb->andWhere('finished=0');

        return $qb;
    }
}