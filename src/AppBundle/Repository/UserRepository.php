<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 12.12.2016
 * Time: 10:48
 */

namespace AppBundle\Repository;

use AppBundle\Entity\ChatMessage;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    /** @var array */
    private $cache = array();

    /**
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
}