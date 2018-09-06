<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 24.01.2017
 * Time: 17:13
 */

namespace tests\AppBundle\Map;


use AppBundle\Entity\Map;
use AppBundle\Exception\UnknownMapException;
use Tests\AppBundle\MapTestCase;


class MapLoaderTest extends MapTestCase
{

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    public function testLoadUnknownMap()
    {
        $mapLoader = $this->getMapLoader();
        $this->expectException(UnknownMapException::class);
        $mapLoader->createMapDataFromFiles(2);
    }

    public function testLoadMap1()
    {
        $mapLoader = $this->getMapLoader();

        /** @var Map $map */
        $mapData = $mapLoader->createMapDataFromFiles(1);

        $this->assertEquals(1, $mapData->getId(), 'MapData reflects id');
        $this->assertEquals("Die Erste", $mapData->name, 'Right name for map');
        $this->assertEquals("Didi", $mapData->author, 'Right author for map');
        $this->assertEquals(true, $mapData->active, 'Map is active');
        $this->assertEquals(5, $mapData->players, 'Number of players is correct');
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7], $mapData->cps, 'All checkpoints listed');
    }

    public function testLoadMap30()
    {
        $mapLoader = $this->getMapLoader();

        /** @var Map $map */
        $mapData = $mapLoader->createMapDataFromFiles(30);

        $this->assertEquals(30, $mapData->getId(), 'MapData reflects id');
        $this->assertEquals("(unbekannt)", $mapData->name, 'Right name for map');
        $this->assertEquals("(unbekannt)", $mapData->author, 'Right author for map');
        $this->assertEquals(false, $mapData->active, 'Map is not active');
        $this->assertEquals(10, $mapData->players, 'Number of players is correct');
    }

    public function testGetMapIds()
    {
        $mapLoader = $this->getMapLoader();
        $ids = $mapLoader->getAvailableMapIds();
        $this->assertEquals([1, 30, 200, 1006], $ids);
    }
}
