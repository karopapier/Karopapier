<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 12.12.2016
 * Time: 10:48
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class MapRepository extends EntityRepository
{
    /**
     * @return Map[]
     */
    public function getActiveMaps()
    {
        $qb = $this->createQueryBuilder('m')
            ->where('m.active=true')
            ->orderBy('m.id');
        $maps = $qb->getQuery()->execute();

        return $maps;
    }

    public function ensureMapIdExists($mapId)
    {
        $connection = $this->getEntityManager()->getConnection();
        $id = (int)$mapId;
        $res = $connection->executeQuery('SELECT M_ID from karo_maps WHERE M_ID='.$id);
        if ($res->rowCount() === 1) {
            return true;
        }

        // map does no exists, need to create map entry
        $sql = "INSERT INTO `karo_maps` (`M_ID`, `Name`, `Code`, `Author`, `Comment`, `Night`, `Record`, `Starties`, `nb_cps`, `cps_list`, `cps_rec`, `Active`, `rating`) VALUES ('".$id."', '-', 'XXX', '-', '-', '0', '0', '0', '0', '', '0', '0', '0');";
        $connection->executeQuery($sql);
    }
}