<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 20.05.2016
 * Time: 00:10
 */

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Game;
use AppBundle\Repository\GameRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class GameController extends AbstractApiController
{

    /**
     * @var GameRepository
     */
    private $gameRepository;

    public function __construct(GameRepository $gameRepository)
    {
        $this->gameRepository = $gameRepository;
    }

    /**
     * @Route("/games/{id}", name="api_game")
     * @param Game $game
     */
    public function showAction(Request $request, $id, SerializerInterface $serializer)
    {
        $options = [
            'players' => $request->query->getBoolean('players', false),
            'moves' => $request->query->getBoolean('moves', false),
            'mapcode' => $request->query->getBoolean('mapcode', false),
        ];

        // wenn Moves brauchen wir natuerlich auch players
        if ($options['moves']) {
            $options['players'] = true;
        }

        $game = $this->getGameFromOptions($id, $options);

        $json = $serializer->serialize($game, "json", $options);

        $response = JsonResponse::fromJsonString($json);
        $response->setCallback($request->get("callback"));

        return $response;
    }

    private function getGameFromOptions($id, $options)
    {
        if ($options['moves']) {
            return $this->gameRepository->findGameWithPlayersAndMoves($id);
        }

        if ($options['players']) {
            return $this->gameRepository->findGameWithPlayers($id);
        }

        return $this->gameRepository->find($id);
    }

}