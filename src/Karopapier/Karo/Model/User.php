<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 03.08.2015
 * Time: 17:28
 */

namespace Karopapier\Karo\Model;

class User
{
    public function getId()
    {
        return $this->U_ID;
    }

    public function getName()
    {
        return $this->Login;
    }

    public function getLogin()
    {
        return $this->Login;
    }
}