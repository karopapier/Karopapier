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
use PHPUnit\Framework\TestCase;


class MapTest extends TestCase
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
}
