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
use AppBundle\Services\ConfigService;
use Psr\Log\LoggerInterface;

class MapLoader
{
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

    private function parseMapFile($data, $mapFile)
    {
        $mapcode = file_get_contents($mapFile);
        $data->mapcode = $mapcode;

        return $data;
    }

    private function parseDescFile($data, $descFile)
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

    private function parseYamlFile($data, $yamlFile)
    {
        // all fields will be added to a custom option section
        // some fields will be used as direct attributes on the entity (like active)
        return $data;

    }
}