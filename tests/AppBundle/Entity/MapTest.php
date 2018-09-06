<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 24.01.2017
 * Time: 17:13
 */

namespace tests\AppBundle\Map;


use AppBundle\DTO\MapData;
use AppBundle\Entity\Map;
use AppBundle\Model\Position;
use Tests\AppBundle\MapTestCase;


class MapTest extends MapTestCase
{
    public function testCreateFromData()
    {
        $md = new MapData(12);
        $md->name = "Karte";
        $md->cps = [1, 5, 7];
        $map = Map::createFromData($md);

        $this->assertEquals(12, $map->getId());
        $this->assertEquals('Karte', $map->getName());
        $this->assertEquals([1, 5, 7,], $map->getCpArray());
    }

    public function testGetField()
    {
        $mapLoader = $this->getMapLoader();
        $mapData = $mapLoader->createMapDataFromFiles(200);

        $map = Map::createFromData($mapData);

        $this->assertEquals(
            'P',
            $map->getFieldAtPosition(new Position(0, 0)),
            'Get correct field from map at position 0 0'
        );

        $this->assertEquals(
            'O',
            $map->getFieldAtPosition(new Position(6, 6)),
            'Get correct field from map at position 6 6'
        );

        $this->assertEquals(
            'Z',
            $map->getFieldAtPosition(new Position(9, 1)),
            'Get correct field from map at position 9 1'
        );

        $this->assertEquals(
            'Y',
            $map->getFieldAtPosition(new Position(9, 2)),
            'Get correct field from map at position 9 2 '
        );

        $this->assertEquals(
            '7',
            $map->getFieldAtPosition(new Position(12, 7)),
            'Get correct field from map at position 12 7'
        );
    }
}
