<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 15.03.2018
 * Time: 09:23
 */

namespace AppBundle\ValueObject;


use AppBundle\Exception\InvalidUsernameException;

/**
 * Class Username
 * @package AppBundle\ValueObject
 */
class Username
{
    private $username = '';

    /**
     * Username constructor.
     * @param $username
     * @throws InvalidUsernameException
     */
    public function __construct($username)
    {
        $username = (string)$username;
        if (preg_match("/^[0-9a-z_\-äöüÄÖÜß\. @\[\]]{2,25}$/i", $username)) {
            $this->username = $username;
        } else {
            throw new InvalidUsernameException('Invalid Username: '.$username);
        }
    }

    public function __toString()
    {
        return $this->username;
    }

    public function equals(Username $other)
    {
        return ($this->username === $other->username);
    }

    public function getCanonical()
    {
        return strtolower($this->username);
    }

    public function getFolder()
    {
        return substr($this->getCanonical(), 0, 3);
    }
}