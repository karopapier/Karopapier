<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 18.05.2016
 * Time: 23:38
 */

namespace AppBundle\Security;

use AppBundle\Services\ConfigService;
use Symfony\Component\HttpFoundation\Cookie;

class LegacyCookieSetter
{
    private $host;

    public function __construct(ConfigService $configService)
    {
        $this->host = $configService->get('host');
    }

    public function getCookie($id, $password)
    {
        $year = 60 * 60 * 24 * 30 * 12;
        $md5password = md5($password);
        $karocoded = base64_encode($id."|--|".$md5password);

        return new Cookie("KaroKeks", $karocoded, time() + $year, "", $this->host, 0);
    }

    public function setCookie($id, $password)
    {
        $year = 60 * 60 * 24 * 30 * 12;
        $md5password = md5($password);
        $karocoded = base64_encode($id."|--|".$md5password);
        setcookie("KaroKeks", $karocoded, time() + $year, "", $this->host, 0);
        setcookie("karopwd", $md5password, time() + $year, "", $this->host, 0);

        return true;
    }

    public function clearCookie()
    {
        $past = time() - 1000;
        setcookie("KaroKeks", "gone", $past, "", $this->host, 0);
        setcookie("karopwd", "gone", $past, "", $this->host, 0);
    }
}
