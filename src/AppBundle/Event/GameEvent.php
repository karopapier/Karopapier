<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 14.12.2016
 * Time: 20:13
 */

namespace AppBundle\Event;


use AppBundle\Entity\Game;
use Symfony\Component\EventDispatcher\Event;

class GameEvent extends Event
{

    /**
     * GameEvent constructor.
     * @param Game $game
     */
    public function __construct(Game $game)
    {
    }
}