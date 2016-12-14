<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 12.12.2016
 * Time: 10:48
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

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
}