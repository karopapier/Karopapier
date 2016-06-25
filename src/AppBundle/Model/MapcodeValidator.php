<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 25.06.2016
 * Time: 00:38
 */

namespace AppBundle\Model;


use AppBundle\Entity\Map;

class MapcodeValidator
{
    private $mapcode;
    private $map;

    public function __construct($mapcode)
    {
        $this->map = new Map();
        $this->map->setMapcode($mapcode);
    }

    public function validate()
    {
        return true;
    }
}