<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 12.12.2016
 * Time: 10:48
 */

namespace AppBundle\Repository;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PDO;
use Symfony\Bridge\Doctrine\RegistryInterface;

class MapvoteRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Mapvote::class);
    }

    public function getAverage($mapId)
    {
        $connection = $this->getEntityManager()->getConnection();

        $sql = "SELECT AVG(Vote) as rating FROM `karo_mapvotes` WHERE `M_ID` = :id";
        $stmt = $connection->prepare($sql);

        if ($stmt->execute(['id' => $mapId])) {
            if ($stmt->rowCount() === 0) {
                return 0;
            }
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row['rating']) {
                return $row['rating'];
            }
        }

        return 0;
    }
}