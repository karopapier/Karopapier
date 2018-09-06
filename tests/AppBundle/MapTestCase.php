<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 05.09.2018
 * Time: 23:58
 */

namespace Tests\AppBundle;


use AppBundle\Map\MapLoader;
use AppBundle\Services\ConfigService;
use Doctrine\Common\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class MapTestCase extends TestCase
{
    public function getMapLoader()
    {
        $configService = $this->createMock(ConfigService::class);
        $configService->expects($this->once())
            ->method('get')
            ->willReturn(__DIR__.'/../../src/AppBundle/DataFixtures/Map');

        $logger = $this->createMock(LoggerInterface::class);
        $em = $this->createMock(ObjectManager::class);

        return new MapLoader($configService, $em, $logger);
    }
}