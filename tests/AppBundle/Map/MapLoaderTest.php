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
use AppBundle\Map\MapLoader;
use AppBundle\Services\ConfigService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;


class MapLoaderTest extends TestCase
{

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    private function getMapLoader()
    {
        $configService = $this->createMock(ConfigService::class);
        $configService->expects($this->once())
            ->method('get')
            ->willReturn(__DIR__.'/../../../src/AppBundle/DataFixtures/Map');

        $logger = $this->createMock(LoggerInterface::class);

        return new MapLoader($configService, $logger);
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

        $this->assertEquals("Die Erste", $mapData->name, 'Right name for map');
        $this->assertEquals("Didi", $mapData->author, 'Right author for map');
        $this->assertEquals(true, $mapData->active, 'Map is active');
        $this->assertEquals(5, $mapData->players, 'Number of players is correct');
    }

    public function testLoadMap30()
    {
        $mapLoader = $this->getMapLoader();

        /** @var Map $map */
        $mapData = $mapLoader->createMapDataFromFiles(30);

        $this->assertEquals("(unbekannt)", $mapData->name, 'Right name for map');
        $this->assertEquals("(unbekannt)", $mapData->author, 'Right author for map');
        $this->assertEquals(false, $mapData->active, 'Map is not active');
        $this->assertEquals(10, $mapData->players, 'Number of players is correct');
    }
}
