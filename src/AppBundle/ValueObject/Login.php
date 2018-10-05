<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 15.03.2018
 * Time: 09:23
 */

namespace AppBundle\ValueObject;


use AppBundle\Exception\InvalidLoginException;

/**
 * Class Login
 * @package AppBundle\ValueObject
 */
class Login
{
    private $login = '';

    /**
     * Login constructor.
     * @param $login
     * @throws InvalidLoginException
     */
    public function __construct($login)
    {
        $login = (string)$login;
        if (preg_match("/^[0-9a-z_\-äöüÄÖÜß\. @\[\]]{2,25}$/i", $login)) {
            $this->login = $login;
        } else {
            throw new InvalidLoginException('Invalid Login: '.$login);
        }
    }

    public function __toString()
    {
        return $this->login;
    }

    public function equals(Login $other)
    {
        return ($this->login === $other->login);
    }

    public function getCanonical()
    {
        return strtolower($this->login);
    }

    public function getFolder()
    {
        return substr($this->getCanonical(), 0, 3);
    }
}