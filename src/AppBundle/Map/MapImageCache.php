<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 03.09.2018
 * Time: 22:12
 */

namespace AppBundle\Map;


use AppBundle\Entity\Map;
use AppBundle\Model\MapImage;
use AppBundle\Services\ConfigService;
use AppBundle\ValueObject\MapImageOptions;

class MapImageCache
{
    /**
     * @var ConfigService
     */
    private $config;
    /**
     * @var MapImageRenderer
     */
    private $mapImageRenderer;

    public function __construct(ConfigService $config, MapImageRenderer $mapImageRenderer)
    {
        $this->cacheDir = $config->get('map_image_cache_dir');
        $this->webDir = $config->get('map_image_web_dir');
        $this->mapImageRenderer = $mapImageRenderer;
    }

    public function getUrl(MapImage $mapImage)
    {
        $path = $this->getFilePath($mapImage);

        if (!$this->isCached($mapImage)) {
            // create image and save it
            $binary = $this->getBinary($mapImage);
            file_put_contents($path, $binary);
        }

        return $this->getWebPath($mapImage);

    }

    public function getBinary($mapImage)
    {
        return $this->mapImageRenderer->getImageString($mapImage);
    }

    public function getThumbnail(Map $map)
    {
        $options = new MapImageOptions();
        $options->setSize(1);
        $options->setBorder(0);

        return new MapImage($map, $options);
    }

    private function getWebPath(MapImage $mapImage)
    {
        return $this->webDir.'/'.$mapImage->getFilename();
    }

    public function getCachedFilePath(MapImage $mapImage)
    {
        $path = $this->getFilePath($mapImage);

        if (!$this->isCached($mapImage)) {
            // create image and save it
            $binary = $this->getBinary($mapImage);
            file_put_contents($path, $binary);
        }

        return $path;
    }

    private function getFilePath(MapImage $mapImage)
    {
        return $this->cacheDir.'/'.$mapImage->getFilename();
    }

    private function isCached(MapImage $mapImage)
    {
        $fn = $this->getFilePath($mapImage);

        return (file_exists($fn));
    }
}