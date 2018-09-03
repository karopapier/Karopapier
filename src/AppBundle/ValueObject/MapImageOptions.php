<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 03.09.2018
 * Time: 17:32
 */

namespace AppBundle\ValueObject;


class MapImageOptions
{
    public $size = 12;
    public $border = 1;
    public $cps = true;
    public $specles = true;
    public $filetype = 'png';
    public $night = false;

    public function __construct()
    {
        $this->availableFiletypes = ['jpg', 'png', 'gif'];
    }

    public function setFiletype($filetype)
    {
        $filetype = strtolower($filetype);
        if (!in_array($filetype, $this->availableFiletypes)) {
            throw new \Exception('UNKNOWN FILE TYPE: "'.$filetype.'"');
        }
        $this->filetype = $filetype;
    }

    public function setSize($size)
    {
        $size = (int)$size;
        $size = max(1, $size);
        $size = min(20, $size);
        $this->size = $size;

        if ($size < 8) {
            $this->specles = false;
        }
    }

    public function setBorder($border)
    {
        $border = (int)$border;
        $border = max(0, $border);
        $border = min($border, 5);
        $this->border = $border;
    }

    public function setCps($cps)
    {
        $this->cps = (bool)$cps;
    }
}