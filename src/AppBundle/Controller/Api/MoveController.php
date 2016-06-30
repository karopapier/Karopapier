<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 30.06.2016
 * Time: 13:39
 */

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Game;
use AppBundle\Entity\Move;
use AppBundle\Model\Motion;
use AppBundle\Model\Position;
use AppBundle\Model\Vector;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class MoveController
{

    /**
     * @Route("/game/{id}/move", name="api_move_add", requirements={"id": "\d+"})
     * @Security("has_role('ROLE_USER')")
     * @Method("POST")
     * @param Game $game
     */
    public function addAction(Request $request, Game $game)
    {
        throw  new AccessDeniedHttpException;
        //retrieve request body
        $jsonString = $request->getContent();
        $json = json_decode($jsonString, true);

        $x = $json['x'];
        $y = $json['y'];
        $xv = $json['xv'];
        $yv = $json['yv'];

        $user = $this->getUser();

        $move = new Move();
        $move->setMotion(
            new Motion(
                new Position($x, $y),
                new Vector($xv, $yv)
            )
        );
        $move->setUser($user);
        $move->setGame($game);

        $dm = $this->get("doctrine")->getManager();
        $dm->persist($move);
        $dm->flush();

        /*
        CORS
        
        usersession
        
        parameter prüfen
        GID, xpos, ypos, xvec, yvec, startx, starty, movemessage
        
        bei startx/y prüfen, ob nicht schon gezoge (Warum separat behandeln??)
        
        
        
        TRANSACTION!!!!
        game sperren (select for update)
        
        prüfen, ob xvec/yvec änderung in x/y <=1 
        
        Prüfen, ob vec und pos zueinander passen
        
        Prüfen, ob zug möglich
        
        
        Bei Startpunkt prüfen, ob es wirklich Startpunkte sind
        und nicht schon besetzt
        
        Zug ins logfile
        
        Prüfen, welche Checkpoints überfahren werden
        
        Zug in movestable
        ggf message reinschreiben
        ggf mit CP info
        
        
        Prüfen, ob ins Ziel gezogen wird und alle Checkpoins gecheckt sind
        
        Bei Zielzug
        - Position errechnen
        - einen Parc-Fermée Zug hinterher
        - log
        - finished setzen
        - Realtime info über beendet
        - Streckenrekord prüfen
        
        Sofern noch spieler übrig
        NEXTONE
        (ggf mit neuer runde)
        sonst spiel beendet markieren
        
        COMMIT
        
        Real time für Zug an alle
        Real time für user dran dec
        Real time für next one dran inc
        real time game finished?
        
        Prüfen ob next one eine email will? Senden
        
        Thumbnail update
        Wollust update
        
        Ausgabe,wie's weiter geht
        */
    }
}