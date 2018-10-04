<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 12.12.2016
 * Time: 10:48
 */

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /** @var array */
    private $cache = array();

    /**
     * @return User
     */
    public function getUserForLogin($login)
    {
        if (isset($this->cache[$login])) {
            return $this->cache[$login];
        }

        /** @var User $user */
        $user = $this->findOneBy(array("login" => $login));
        $this->cache[$login] = $user;

        return $user;
    }

    /**
     * @param string $login
     * @return User[]
     */
    public function getActiveUsers($login = "")
    {
        $filter = [
            "active" => true,
        ];
        $sort = [
            "id" => "ASC",
        ];

        $qb = $this->createQueryBuilder('u')
            ->where("u.active=true");
        if ($login) {
            $qb->andWhere('u.login LIKE :login')
                ->setParameter('login', '%'.$login.'%');
        }
        $query = $qb->getQuery();

        return $query->execute();
    }

    /**
     * GLobal blocker list
     * @return User[]
     */
    public function getBlockerList()
    {
        $qb = $this->createQueryBuilder('u')
            ->where("u.active=true")
            ->orderBy('u.nbDran', 'desc');
        $query = $qb->getQuery();

        return $query->execute();
    }
}