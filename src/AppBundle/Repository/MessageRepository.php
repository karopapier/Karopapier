<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 12.12.2016
 * Time: 10:48
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Message;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class MessageRepository extends EntityRepository
{
    /**
     * @param string $login
     * @return Message[]
     */
    public function getMessages(User $user)
    {
        $sort = [
            "created_at" => "ASC",
        ];

        $qb = $this->createQueryBuilder('m')
            ->where("m.userId=:userId")
            ->setParameter('userId', $user->getId());
        $query = $qb->getQuery();

        return $query->execute();
    }
}