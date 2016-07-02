<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 02.07.2016
 * Time: 19:44
 */

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\BrowserKit\Cookie;

class KaroWebTestCase extends WebTestCase
{
    public function logIn(Client $client, $userid, $password)
    {
        $md5password = md5($password);
        $karocoded = base64_encode($userid . "|--|" . $md5password);
        $cookie = new Cookie("KaroKeks", $karocoded);
        $client->getCookieJar()->set($cookie);
        return true;
    }

}