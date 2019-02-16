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
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PDO;
use Symfony\Bridge\Doctrine\RegistryInterface;

class MessageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * @param string $login
     * @return Message[]
     */
    public function getMessages(User $user)
    {
        $sort = [
            "created_at" => "DESC",
        ];

        $qb = $this->createQueryBuilder('m')
            ->where("m.userId=:userId")
            ->setParameter('userId', $user->getId());
        $qb->setMaxResults(150);
        $query = $qb->getQuery();

        return $query->execute();
    }

    public function getContactIds(User $user)
    {
        $sql = "select distinct contact_id from karo_message where user_id=".$user->getId()." and is_deleted=false";
        $params = [];
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute($params);

        //I used FETCH_COLUMN because I only needed one Column.
        $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $ids = array_map(
            function ($id) {
                return (int)$id;
            },
            $ids
        );

        return $ids;
    }

    public function getUnreadById($id)
    {
        $sql = 'SELECT count(id) as uc FROM karo_message WHERE user_id = '.$id.' AND rxtx="rx" AND read_at IS NULL';
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();

        return (int)$stmt->fetchColumn();
    }

    public function setAllRead($userId, $contactId)
    {
        $now = date('Y-m-d H:i:s');
        $sql = 'UPDATE karo_message SET read_at="'.$now.'" WHERE user_id = :uid AND contact_id= :cid AND rxtx=:rxrx AND read_at IS NULL';
        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);

        //I read all
        $stmt->execute(
            [
                "uid" => $userId,
                "cid" => $contactId,
                "rxrx" => "rx",
            ]
        );

        //tell other on I read all
        $stmt->execute(
            [
                "uid" => $contactId,
                "cid" => $userId,
                "rxrx" => "tx",
            ]
        );
    }
}