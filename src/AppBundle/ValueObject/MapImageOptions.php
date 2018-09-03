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
        if (($size) && ($size >= 1)) {
            $this->size = $size;
        }

        if ($size < 8) {
            $this->specle = false;
        }
    }

}