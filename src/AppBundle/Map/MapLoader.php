<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 14.04.2018
 * Time: 15:49
 */

namespace AppBundle\Map;


use AppBundle\DTO\MapData;
use AppBundle\Entity\Map;
use AppBundle\Exception\UnknownMapException;
use AppBundle\Model\Mapcode;
use AppBundle\Repository\MapRepository;
use AppBundle\Repository\MapvoteRepository;
use AppBundle\Services\ConfigService;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class MapLoader
{
    private $mapDirectory;
    /**
     * @var ConfigService
     */
    private $configService;
    /**
     * @var ObjectManager
     */
    private $em;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ConfigService $configService, ObjectManager $em, LoggerInterface $logger)
    {
        $this->mapDirectory = $configService->get('map_dir');
        $this->configService = $configService;
        $this->em = $em;
        $this->logger = $logger;
    }

    public function loadMapFolder()
    {
        // check all *.map files
        $mapIds = $this->getAvailableMapIds();

        /** @var MapvoteRepository $mapvoteRepo */
        $mapvoteRepo = $this->em->getRepository(MapvoteRepository::class);

        // create data and ensure/update map entity
        foreach ($mapIds as $mapId) {
            $this->logger->info('Loading Map '.$mapId);
            $mapData = $this->createMapDataFromFiles($mapId);

            /** @var MapRepository $repo */
            $repo = $this->em->getRepository(Map::class);
            $repo->ensureMapIdExists($mapId);
            // save
            /** @var Map $map */
            $map = $repo->find($mapId);
            $map->updateFromData($mapData);

            $rating = $mapvoteRepo->getAverage($mapId);
            $map->updateRating($rating);

            $this->em->persist($map);
            $this->em->flush();
        }

    }

    public function getAvailableMapIds()
    {
        $finder = new Finder();
        $mapFiles = $finder->in($this->mapDirectory)->name('*.map')->files();

        $ids = [];
        foreach ($mapFiles as $file) {
            $ids[] = (int)str_replace('.map', '', $file->getFileName());
        }
        sort($ids);

        return $ids;
    }

    public function createMapDataFromFiles($mapId)
    {
        $mapId = (int)$mapId;
        $mapFile = $this->mapDirectory.'/'.$mapId.'.map';
        $descFile = $this->mapDirectory.'/'.$mapId.'.desc';
        $yamlFile = $this->mapDirectory.'/'.$mapId.'.yml';
        if (!file_exists($mapFile)) {
            throw new UnknownMapException('No map file for id '.$mapId);
        }

        $data = new MapData($mapId);
        $this->parseMapFile($data, $mapFile);
        $this->parseDescFile($data, $descFile);
        $this->parseYamlFile($data, $yamlFile);

        return $data;
    }

    private function parseMapFile(MapData $data, $mapFile)
    {
        $rawmapcode = file_get_contents($mapFile);
        $mapcode = new Mapcode($rawmapcode);
        $data->mapcode = $mapcode;
        $mapcodeArray = $mapcode->toArray();
        $data->cps = $mapcodeArray['cps'];
        $data->players = $mapcodeArray['players'];

        return $data;
    }

    private function parseDescFile(MapData $data, $descFile)
    {
        $name = '(unbekannt)';
        $author = '(unbekannt)';
        if (file_exists($descFile)) {
            $desc = file($descFile);

            $name = trim($desc[0]);
            if (isset($desc[1])) {
                $author = trim($desc[1]);
            }
        }

        $data->name = $name;
        $data->author = $author;

        return $data;
    }

    /**
     * @param MapData $data
     * @param $yamlFile
     * @return MapData
     */
    private function parseYamlFile(MapData $data, $yamlFile)
    {
        if (!file_exists($yamlFile)) {
            return $data;
        }

        // all option fields will be added to a custom option section
        // some fields will be used as direct attributes on the entity (like active)
        $options = Yaml::parseFile($yamlFile);
        if (array_key_exists('active', $options)) {
            $data->active = $options['active'];
        }

        return $data;
    }
}