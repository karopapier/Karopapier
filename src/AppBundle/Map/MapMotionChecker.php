<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 06.09.2018
 * Time: 02:00
 */

namespace AppBundle\Map;


use AppBundle\Entity\Map;
use AppBundle\Model\Motion;

class MapMotionChecker
{
    private $invalidFields;

    public function __construct()
    {
        $this->invalidFields = [
            'X',
            'Y',
            'Z',
            'T',
            'V',
            'W',
            'L',
            'G',
            'N',
            'P',
        ];
    }

    /**
     * @param Map $map
     * @param Motion $mo
     * @return bool
     */
    public function isValidMotion(Map $map, Motion $mo)
    {
        $fields = $map->getPassedFields($mo);
        // intersect passed with bad ones
        $bads = array_intersect($fields, $this->invalidFields);

        // if there are bad fields passes (in array), it is invalid
        return (count($bads) === 0);
    }
}