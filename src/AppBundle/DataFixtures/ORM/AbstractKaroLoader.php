<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 12.12.2016
 * Time: 09:19
 */

namespace AppBundle\DataFixtures\ORM;


use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

abstract class AbstractKaroLoader extends AbstractFixture implements FixtureInterface
{
    public function makeUser(ObjectManager $manager, $handle, $data)
    {
        $user = new User();
        $user->init($data);
        $manager->persist($user);
        $this->setReference($handle, $user);
    }
}