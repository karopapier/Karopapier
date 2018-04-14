<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 14.04.2018
 * Time: 16:38
 */

namespace AppBundle\DTO;

class MapData
{
    private $id;
    public $name;
    public $mapcode;
    public $author;
    public $active = true;
    public $players = 0;
    public $cps = [];
    public $night = false;
}