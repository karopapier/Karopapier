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
use Symfony\Component\Serializer\SerializerInterface;

class GameController extends AbstractApiController
{
    /**
     * @Route("/games/{id}", name="api_game")
     * @param Game $game
     */
    public function showAction(Game $game, SerializerInterface $serializer)
    {
        $json = $serializer->serialize($game, "json");

        return JsonResponse::fromJsonString($json);
    }

}