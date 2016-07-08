<?php

/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 02.07.2016
 * Time: 20:17
 */

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;


class LoadUserData extends AbstractFixture implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $didi = new User();
        $didi->init([
                "id" => 1,
                "login" => "Didi",
                "passwd" => "qwerasdf",
                "email" => "didi@karopapier.de",
        ]);
        $this->setReference('didi', $didi);
        $manager->persist($didi);
        $manager->flush();
    }
}