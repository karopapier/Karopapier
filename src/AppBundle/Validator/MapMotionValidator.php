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

    /**
     * @param Motion $mo
     * @param Game optional to check for rules
     * @return bool
     */
    public function isValidMotion(Map $map, Motion $mo)
    {
        $fields = $map->getPassedFields($mo);

        if (in_array('X', $fields)) {
            return false;
        }
        if (in_array('Y', $fields)) {
            return false;
        }
        if (in_array('Z', $fields)) {
            return false;
        }
        if (in_array('T', $fields)) {
            return false;
        }
        if (in_array('V', $fields)) {
            return false;
        }
        if (in_array('W', $fields)) {
            return false;
        }
        if (in_array('L', $fields)) {
            return false;
        }
        if (in_array('G', $fields)) {
            return false;
        }
        if (in_array('N', $fields)) {
            return false;
        }
        if (in_array('P', $fields)) {
            return false;
        }

        return true;
    }

}