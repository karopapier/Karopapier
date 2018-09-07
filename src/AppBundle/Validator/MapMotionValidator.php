<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 06.09.2018
 * Time: 02:00
 */

namespace AppBundle\Validator;


use AppBundle\Entity\Map;
use AppBundle\Model\Motion;

class MapMotionValidator
{

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
     * @param Motion $mo
     * @param Game optional to check for rules
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