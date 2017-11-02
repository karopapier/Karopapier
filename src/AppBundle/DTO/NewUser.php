<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 02.11.2017
 * Time: 23:28
 */

namespace AppBundle\DTO;

use Symfony\Component\Validator\Constraints as Assert;


class NewUser
{
    /**
     * @Assert\Regex("/^[A-Za-z0-9-_\[\]\(\)ÄÖÜäöüß;=?\.@ ]{3,20}$/")
     * @var string
     */
    private $login;

    public function __construct($login)
    {
        $this->login = $login;
    }
}