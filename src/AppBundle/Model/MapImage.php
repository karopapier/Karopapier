<?php

namespace AppBundle\Model;

use AppBundle\Entity\Map;
use AppBundle\ValueObject\MapImageOptions;

class MapImage
{
    /**
     * @var Map
     */
    private $map;
    /**
     * @var MapImageOptions
     */
    private $options;

    public function __construct(Map $map, MapImageOptions $options)
    {
        $this->map = $map;
        $this->options = $options;
    }

    public function getMap()
    {
        return $this->map;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getFilename()
    {
        $id = $this->map->getId();
        $cpString = (int)$this->options->cps;
        $filename = sprintf(
            "%s_%s_%s_%s.%s",
            $id,
            $this->options->size,
            $this->options->border,
            $cpString,
            $this->options->filetype
        );

        return $filename;
    }

    public function getImageWidth()
    {
        $fieldsize = $this->getFieldsize();

        return $this->map->getNbCols() * $fieldsize;
    }

    public function getImageHeight()
    {
        $fieldsize = $this->getFieldsize();

        return $this->map->getNbRows() * $fieldsize;
    }

    private function getFieldsize()
    {
        return $this->options->size + $this->options->border;
    }

    public function setSizeByWidthOrHeight($width, $height)
    {
        if ($width > 0) {
            $this->options->setSize(ceil($width / $this->map->getNbCols()));

            return;
        }

        if ($height > 0) {
            $this->options->setSize(ceil($height / $this->map->getNbRows()));

            return;
        }
    }
}
