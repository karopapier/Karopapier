<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 12.12.2016
 * Time: 10:48
 */

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use AppBundle\ValueObject\Login;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PDO;
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
     * @deprecated Wird nur vom Chat-Reconstruct verwendet
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

    public function findByLogin(Login $login)
    {
        return $this->findOneBy(["login" => (string)$login]);
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

    public function getUserBlockerData(User $user)
    {
        $sql = "SELECT karo_games.U_ID as id, karo_user.login, count( karo_games.G_ID ) AS blocked
    FROM karo_games, karo_teilnehmer, karo_user
    WHERE karo_games.U_ID <>:uid
    AND karo_user.U_ID = karo_games.U_Id
    AND karo_games.G_ID = karo_teilnehmer.G_ID
    AND karo_teilnehmer.U_ID =:uid
    AND karo_teilnehmer.status >0
    AND karo_teilnehmer.finished =0
    AND karo_games.finished =0
    GROUP BY karo_games.U_ID, karo_user.login
    ORDER BY blocked DESC, login";
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $res = $stmt->execute(['uid' => $user->getId()]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}