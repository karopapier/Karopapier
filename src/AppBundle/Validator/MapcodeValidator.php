<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 25.06.2016
 * Time: 00:38
 */

namespace AppBundle\Validator;

class MapcodeValidator
{
    private $mapcode;

    public function __construct($mapcode)
    {
        $this->map = new Map();
        $this->map->setMapcode($mapcode);
    }

    public function validate()
    {
        //caps lock
        //dimensionen <40x40
        //S
        //F
        //Rand
        //parc

        //keine unbekannten Zeichen

        //run accessible
        //accessible onOf("F");
        //each CP accessible onOf(cp)

        return true;
    }
}