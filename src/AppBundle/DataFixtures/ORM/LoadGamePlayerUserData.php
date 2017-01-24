<?php

/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 02.07.2016
 * Time: 20:17
 */

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;


class LoadGamePlayerUserData extends AbstractKaroLoader
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

        $xosofox = $this->makeUser($manager, "xosofox", [
                "id" => 2,
                "login" => "xosofox",
                "passwd" => "qwerasdf",
                "email" => "xosofox@karoworld.de",
                "signupdate" => new \DateTime("2010-01-01"),
                "reallastvisit" => new \DateTime("2016-01-01")
        ]);

        $fred = $this->makeUser($manager, "fred", [
                "id" => 3,
                "login" => "fred",
                "passwd" => "qwerasdf",
                "email" => "fred@karoworld.de",
                "signupdate" => new \DateTime("2010-01-01"),
                "reallastvisit" => new \DateTime("2016-01-01")
        ]);

        $manager->flush();
    }
}