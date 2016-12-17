<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 17.12.2016
 * Time: 19:03
 */

namespace AppBundle\Services\User;


use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;

class DistanceCalculator
{

    /** @var EntityManager */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function calculateDistance(User $user)
    {
        $id = $user->getId();
        $em = $this->em;

        //Driven milimeters
        //SELECT SUM( SQRT( POW( x_vec, 2 ) + POW( y_vec, 2 ) ) * 5 ) from karo_moves where U_ID=1
        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT SUM( SQRT( POW( x_vec, 2 ) + POW( y_vec, 2 ) ) * 5 ) as distance from karo_moves where U_ID=:id");
        $statement->bindValue('id', $id);
        $statement->execute();
        $results = $statement->fetchAll();
        $distance = 0;
        if ((count($results)) > 0) {
            return $results[0]["distance"];
        }
        return 0;
    }

    public function updateDistance(User $user)
    {
        $distance = $this->calculateDistance($user);
        $this->setDistance($user, $distance);
        return $distance;
    }

    public function setDistance(User $user, $distance)
    {
        $user->setDistance($distance);
        $this->em->persist($user);
        $this->em->flush($user);
    }
}