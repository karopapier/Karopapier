<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 20.05.2016
 * Time: 00:11
 */

namespace AppBundle\Controller\Api;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends Controller
{
    /**
     *
     * @Route("/")
     * @Template("api/browser.html.twig")
     */
    public function apiBrowserAction()
    {
        $urls = array(
            "/api/user/1" => "APIv2",
            "/api/user/check" => "APIv2",
            "/api/map/1" => "APIv2",
            "/api/chat/last" => "APIv2",
            "/api/contacts" => "APIv2",
            "/api/messages" => "APIv2",
            "/api/chat?start=357194&limit=20" => "APIv2",
            "/api/chat?start=11965&limit=1" => "APIv2",
            "/api/user/773/dran" => "APIv2",
            "/api/user/Botrix/dran.json" => "Legacy API",
            "/api/user/blockerlist.json" => "Legacy API",
            "/api/user/1/blocker.json" => "Legacy API",
            "/api/game/44773/info.json" => "Legacy API",
            "/api/games/44773/info" => "Legacy API",
            "/api/game/44773/details.json" => "Legacy API",
            "/api/games/44773/details" => "Legacy API",
            "/api/user/list.json" => "Legacy API",
            "/api/map/list.json" => "Legacy API",
            "/api/map/list.json?nocode=true" => "Legacy API",
            "/api/map/list.json?players=30" => "Legacy API",
            "/api/mapcode/1.json" => "Legacy API",
            "/api/mapcode/1.txt" => "Legacy API",
            "/api/map/1/vote.json" => "Legacy API",
            "/api/map/1/vote.json?users=[1,773,213,516]" => "Legacy API",
            "/api/chat/users.json" => "Legacy API",
            "/api/chat/users" => "APIv2, planned",
            "/api/chat/list.json" => "Legacy API",
            "/api/chat/list.json?limit=1" => "Legacy API",
            "/api/chat/list.json?start=11965&limit=1" => "Legacy API",
            "/api/games?user=1" => "Legacy API",
            "/api/games?user=1&finished=true" => "Legacy API",
            "/api/games?user=1&finished=true&limit=1&offset=300" => "Legacy API",
            "/api/games?limit=2&offset=3000" => "Legacy API",
            "http://volkswurst.de/api/" => "kilis API",
            "/api/users/1" => "APIv2, deprecated",
            "/api/user/check.json" => "APIv2, deprecated",
            "/api/users/check" => "APIv2, deprecated",
            "/api/user/1/info.json" => "Legacy API, deprecated",
            "/api/user/Botrix" => "APIv2, deprecated",
            "/api/users/Botrix" => "APIv2, deprecated",
            "/api/user/Botrix/info.json" => "Legacy API, deprecated",
            "/api/user/773/dran.json" => "Legacy API, deprecated",
            "/api/map/1.json" => "Legacy API, deprecated",
        );

        return [
            'urls' => $urls,
        ];
    }
}