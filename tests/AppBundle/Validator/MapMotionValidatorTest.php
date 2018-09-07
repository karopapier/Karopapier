<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 06.09.2018
 * Time: 02:01
 */

namespace Tests\AppBundle\Map;

use AppBundle\Entity\Map;
use AppBundle\Map\MapMotionValidator;
use Tests\AppBundle\MapTestCase;

class MapMotionValidatorTest extends MapTestCase
{
    public function testPositions()
    {
        $loader = $this->getMapLoader();
        $this->assertEquals(1, 1);


        $mapData = $loader->createMapDataFromFiles(200);
        $map = Map::createFromData($mapData);


        $validator =  new MapMotionValidator();



    }
}
