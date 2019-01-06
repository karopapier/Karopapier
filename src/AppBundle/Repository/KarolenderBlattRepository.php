<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 12.12.2016
 * Time: 10:48
 */

namespace AppBundle\Repository;


use AppBundle\Entity\KarolenderBlatt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class KarolenderBlattRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, KarolenderBlatt::class);
    }

    /**
     * @param $m
     * @param $d
     * @return KarolenderBlatt[]
     */
    public function getByMonthDay($m, $d)
    {
        $dayString = "$m-$d";

        return $this->findBy(
            [
                'dayString' => $dayString,
            ]
        );
    }
}