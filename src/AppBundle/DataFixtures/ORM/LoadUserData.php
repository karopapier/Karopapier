<?php

/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 02.07.2016
 * Time: 20:17
 */

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;


class LoadUserData extends AbstractKaroLoader
{
    public function load(ObjectManager $manager)
    {
        $didi = $this->makeUser($manager, "didi", [
                "id" => 1,
                "login" => "Didi",
                "passwd" => "qwerasdf",
                "email" => "didi@karoworld.de",
                "signupdate" => new \DateTime("2010-01-01"),
                "reallastvisit" => new \DateTime("2016-01-01")
        ]);
        $manager->flush();
    }
}