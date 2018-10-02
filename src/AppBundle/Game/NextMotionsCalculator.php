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
use AppBundle\Model\Motion;
use AppBundle\Model\Position;
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
        $motion = $nextPlayer->getCurrentMotion();

        $valid = [];
        if (!$motion) {
            // START? Dann hol alle Startfelder
            /** @var PositionCollection $startPositions */
            $startPositions = $map->getStartPositions();

            // Turn into Motions wit 0|0 vector
            /** @var Position $position */
            foreach ($startPositions as $position) {
                $valid[] = Motion::createFromXYV($position->getX(), $position->getY(), 0, 0);
            }
        } else {
            // check next motions and filter by map
            $all = $motion->getNextMotions();

            // filter valid positions by map
            $valid = [];
            foreach ($all as $motion) {
                if ($this->mapMotionValidator->isValidMotion($map, $motion)) {
                    $valid[] = $motion;
                }
            }
        }

        // filter current player positions
        $blocked = $game->getBlockedPlayersPositions();
        $blockPositionStrings = $blocked->asStringArray();
        $final = [];
        /** @var Motion $motion */
        foreach ($valid as $motion) {
            // if the end position (string) is in the array of blocked position(strings), dont add to final
            $posString = $motion->getPosition()->__toString();
            if (in_array($posString, $blockPositionStrings, true)) {
                continue;
            }
            $final[] = $motion;
        }

        return $final;
    }
}