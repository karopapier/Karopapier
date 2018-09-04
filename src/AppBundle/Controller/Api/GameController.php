<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 20.05.2016
 * Time: 00:10
 */

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Game;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class GameController extends AbstractApiController
{
    /**
     * @Route("/game/{id}", name="api_game")
     * @param Game $game
     */
    public function showAction(Request $request, Game $game, SerializerInterface $serializer)
    {
        $options = [
            'players' => $request->query->getBoolean('players', false),
            'moves' => $request->query->getBoolean('moves', false),
            'mapcode' => $request->query->getBoolean('mapcode', false),
        ];
        $json = $serializer->serialize($game, "json", $options);

        return JsonResponse::fromJsonString($json);
    }

}