<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 14.04.2018
 * Time: 15:49
 */

namespace AppBundle\Map;


use AppBundle\DTO\MapData;
use AppBundle\Exception\UnknownMapException;
use AppBundle\Model\Mapcode;
use AppBundle\Services\ConfigService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Yaml\Yaml;

class MapLoader
{
    private $mapDirectory;

    public function __construct(ConfigService $configService, LoggerInterface $logger)
    {
        $this->mapDirectory = $configService->get('map_dir');
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

        $data = new MapData();
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
            $author = trim($desc[1]);
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

        // all fields will be added to a custom option section
        // some fields will be used as direct attributes on the entity (like active)
        $options = Yaml::parseFile($yamlFile);
        if (array_key_exists('active', $options)) {
            $data->active = $options['active'];
        }

        return $data;
    }
}