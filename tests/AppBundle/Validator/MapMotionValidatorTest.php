<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 06.09.2018
 * Time: 02:01
 */

namespace Tests\AppBundle\Map;

use AppBundle\Entity\Map;
use AppBundle\Map\MapMotionChecker;
use AppBundle\Model\Motion;
use Tests\AppBundle\MapTestCase;

class MapMotionValidatorTest extends MapTestCase
{
    public function testPositions()
    {
        $loader = $this->getMapLoader();
        $this->assertEquals(1, 1);


        $mapData = $loader->createMapDataFromFiles(200);
        $map = Map::createFromData($mapData);

        $expectations = [
            [
                Motion::createFromXYV(18, 18, -6, 3),
                true,
            ],
            [
                Motion::createFromXYV(20, 20, 1, 1),
                false,
            ],
            [
                Motion::createFromXYV(17, 25, -1, 7),
                true,
            ],
        ];

        $validator = new MapMotionChecker();

        foreach ($expectations as $motionString => $combo) {

            /** @var Motion $motion */
            $motion = $combo[0];
            $tf = $combo[1];
            $this->assertEquals(
                $validator->isValidMotion($map, $motion),
                $tf,
                sprintf('Motion %s valitiy %s', $motion, $tf)
            );
        }
    }
}
