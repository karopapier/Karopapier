<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 06.09.2018
 * Time: 01:22
 */

namespace AppBundle\Game;


use AppBundle\Entity\Game;
use AppBundle\Map\MapMotionChecker;
use AppBundle\Model\PositionCollection;

class NextMotionsCalculator
{
    /**
     * @var MapMotionChecker
     */
    private $mapMotionValidator;

    public function __construct(MapMotionChecker $mapMotionValidator)
    {
        $this->mapMotionValidator = $mapMotionValidator;
    }

    public function getNextMotions(Game $game)
    {
        $map = $game->getMap();
        $nextPlayer = $game->getNextPlayer();
        if (!$nextPlayer) {
            return new PositionCollection();
        }
        var_dump($nextPlayer->getUser()->getName());
        $motion = $nextPlayer->getCurrentMotion();
        $all = $motion->getNextMotions();
        $valid = [];

        foreach ($all as $motion) {
            if ($this->mapMotionValidator->isValidMotion($map, $motion)) {
                $valid[] = $motion;
            }
        }

        array_map(
            function ($mo) {
                var_dump($mo->__toString());
            },
            $valid
        );
        die();
        $blocked = $game->getBlockedPlayersPositions();
        var_dump($blocked);

        return $valid;
    }
}