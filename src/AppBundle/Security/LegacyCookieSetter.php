<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 18.05.2016
 * Time: 23:38
 */

namespace AppBundle\Security;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class LegacyCookieSetter
{
    private $host;

    public function __construct($host)
    {
        $this->host = $host;
    }

    public function getCookie($id, $password)
    {
        $year = 60 * 60 * 24 * 30 * 12;
        $md5password = md5($password);
        $karocoded = base64_encode($id . "|--|" . $md5password);
        return new Cookie("KaroKeks", $karocoded, time() + $year, "", $this->host, 0);
    }

    public function setCookie($id, $password)
    {
        $year = 60 * 60 * 24 * 30 * 12;
        $md5password = md5($password);
        $karocoded = base64_encode($id . "|--|" . $md5password);
        setcookie("KaroKeks", $karocoded, time() + $year, "", $this->host, 0);
        setcookie("karopwd", $md5password, time() + $year, "", $this->host, 0);
        return true;
    }
    public function clearCookie()
    {
        $past = time()-1000;
        setcookie("KaroKeks", "gone", $past, "", $this->host, 0);
        setcookie("karopwd", "gone", $past, "", $this->host, 0);
    }
}
