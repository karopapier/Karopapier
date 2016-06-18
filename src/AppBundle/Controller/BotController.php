<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Game;
use AppBundle\Entity\Player;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BotController extends Controller
{
    /**
     * @Route("/bot/{id}", name="bot_show_game")
     */
    public function showAction(Request $request, Game $game)
    {
        //$this->get("game_manager")->load($game);
        $this->get("game_manager")->loadWithMoves($game);
        $gid = $game->getId();

        $dranUser = $game->getDranUser();
        $uid = $dranUser->getId();
        $mapId = $game->getMap()->getId();

        $players = $game->getPlayers();
        /** @var Player $player */
        foreach ($players as $player) {
            echo $player;
            $c = count($player->getMoves());
            echo " " . $c;
            echo " - " . $player->getLastMove();
            echo "<br>\n";
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $id = 1;

        //Gefahrene Meter
        //SELECT SUM( SQRT( POW( x_vec, 2 ) + POW( y_vec, 2 ) ) * .5 )/100 from karo_moves where U_ID=1

        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT SUM( SQRT( POW( x_vec, 2 ) + POW( y_vec, 2 ) ) * .5 )/100 as driven from karo_moves where U_ID=:id");
        $statement->bindValue('id', $id);
        $statement->execute();
        $results = $statement->fetchAll();
        $distance = 0;
        if ((count($results)) > 0) {
            $distance = $results[0]["driven"];
        }

        $user = new User();

        // replace this example code with whatever you need
        return $this->render('user/show.html.twig', array(
                "user" => $user,
                "distance" => $distance
        ));
    }

}
