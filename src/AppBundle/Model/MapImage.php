<?php

namespace AppBundle\Model;

use AppBundle\Entity\Map;

class MapImage
{

    const SIZE = 12;
    const BORDER = 1;

    public function __construct(Map $map)
    {
        $this->map = $map;
        $this->size = self::SIZE;
        $this->border = self::BORDER;
        $this->palette = new MapImagePalette();
        $this->cpEnabled = true;
        $this->specle = true;
        $this->filetype = 'png';

        $this->availableFiletypes = ['jpg', 'png', 'gif'];
    }

    public function getMap()
    {
        return $this->map;
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

    public function setSkipCheckpoints($skip)
    {
        $this->cpEnabled = !($skip);
    }

    public function setSizeFromWidthAndHeight($width, $height)
    {
        $size = $this->map->getSizeForWidthAndHeight($width, $height);
        #echo "$size - ".$this->border . "=\n";
        #exit;
        if ($size > $this->border) {
            $this->setSize($size - $this->border);
        } else {
            $this->setSize($size);
            //circumvent setter to set 0
            $this->border = 0;
        }
    }

    public function setBorder($border)
    {
        if (isset($border) && ($border >= 0)) {
            $this->border = $border;
        }
    }

    public function setFiletype($filetype)
    {
        $filetype = strtolower($filetype);
        if (!in_array($filetype, $this->availableFiletypes)) {
            throw new \Exception('UNKNOWN FILE TYPE: "'.$filetype.'"');
        }
        $this->filetype = $filetype;
    }

    #TODO refactor to use filename and filepath and webfilepath
    public function getFilename()
    {
        $imgdir = '/home/pdietrich/karopapier.de/wrapper/web/images/maps';
        $cpString = $this->cpEnabled ? 1 : 0;
        $filename = $imgdir.'/'.$this->getMap()->getId(
            ).'_'.$this->size.'_'.$this->border.'_'.$cpString.'.'.$this->filetype;

        return $filename;
    }

    #TODO refactor to use filename and filepath and webfilepath
    public function getWebFilename()
    {
        return str_replace(sfConfig::get('sf_web_dir'), '', $this->getFilename());
    }

    public function render()
    {
        $this->init();
        $this->drawFields();

        if ($this->filetype === "png") {
            //imagepng($this->image,$this->getFilename('png'),5,PNG_ALL_FILTERS);
            imagepng($this->image, null, 8, PNG_NO_FILTER);

            // imagepng($this->image, $this->getFilename('png'), 8, PNG_NO_FILTER);

            return true;
        }

        if ($this->filetype === "jpg") {
            imagejpeg($this->image, $this->getFilename('jpg'));

            return true;
        }

        if ($this->filetype === "gif") {
            imagegif($this->image, $this->getFilename('gif'));

            return true;
        }

        die('UNKNOWN FILE TYPE: "'.$filetype.'"');
    }

    private function init()
    {
        $this->fieldSize = $this->size + $this->border;
        $this->sizeX = $this->map->getNbCols() * $this->fieldSize;
        $this->sizeY = $this->map->getNbRows() * $this->fieldSize;
        $this->image = imagecreatetruecolor($this->sizeX, $this->sizeY);

        $this->allocateColors();

        //set bg
        imagefilledrectangle($this->image, 0, 0, $this->sizeX, $this->sizeY, $this->colors['offroadspecle']);

    }

    private function allocateColors()
    {
        $palette = $this->palette->palette;

        foreach ($palette as $colorKey => $colorArray) {
            $this->colors[$colorKey] = imagecolorallocate($this->image, $colorArray[0], $colorArray[1], $colorArray[2]);
        }
    }

    private function drawFields()
    {
        $matrix = $this->getMap()->getMatrix();
        //r = row walker
        //c = column walker
        $maxC = $this->getMap()->getNbCols();
        $maxR = $this->getMap()->getNbRows();
        for ($r = 0; $r < $maxR; $r++) {
            for ($c = 0; $c < $maxC; $c++) {
                $this->drawCodeField($r, $c, $matrix[$r][$c]);
            }
        }
    }

    private function drawCodeField($r, $c, $char)
    {
        $x = $c * ($this->size + $this->border);
        $y = $r * ($this->size + $this->border);

        if (($char == "L") || ($char == "N") || ($char == "V") || ($char == "W") || ($char == "X") || ($char == "Y") || ($char == "Z") || ($char == "O") || ($char == "G")) {
            $fillColor = $this->colors[$char];
            $specleColor = $this->colors[$char.'specle'];
            $this->drawStandardField($x, $y, $fillColor, $specleColor);
        }

        //finish
        if ($char == "F") {
            $this->drawFlagField($x, $y, $this->colors['finish1'], $this->colors['finish2']);
        }

        //checkpoint
        if (is_numeric($char)) {
            if ($this->cpEnabled) {
                $fg = $this->colors['checkpoint'.$char];

                if ($char % 2) {
                    $bg = $this->colors['checkpointBgOdd'];
                } else {
                    $bg = $this->colors['checkpointBgEven'];
                }
                $this->drawFlagField($x, $y, $fg, $bg);
            } else {
                $this->drawCodeField($r, $c, "O");
            }
        }

        //Start
        if ($char == "S") {
            $this->drawStartField($x, $y, $this->colors['start1'], $this->colors['start2']);
        }

        //Parc ferme
        if ($char == "P") {
            $this->drawStandardField($x, $y, $this->colors['parc'], $this->colors['roadspecle'], false); //no specles
        }

        //imagefilledrectangle($this->image,$x+$this->border,$y+$this->border,$x+$this->size,$y+$this->size,$this->colors['road']);
        //header("content-type: image/png");
        //imagepng($this->image);
        //exit;
    }

    /**
     *
     * Draw a "default" field for road or offroad with a solid fillcolor plus random specles
     * @param unknown_type $x Start coordinate of field
     * @param unknown_type $y Start coordinate of field
     * @param unknown_type $c Fill color
     * @param unknown_type $s Specle Color
     */
    private function drawStandardField($x, $y, $c, $s, $specle = true)
    {
        //fill
        imagefilledrectangle(
            $this->image,
            $x,
            $y,
            $x + $this->border + $this->size,
            $y + $this->border + $this->size,
            $c
        );

        $this->drawBorder($x, $y, $s);

        if (($specle) && ($this->specle)) {
            //add random specles
            for ($i = 0; $i < $this->size; $i++) {
                ImageSetPixel($this->image, $x + rand(0, $this->size), $y + rand(0, $this->size), $s);
            }
        }
    }

    /**
     * draw border in given (mostly specle) color to bottom and right
     * @param unknown_type $x
     * @param unknown_type $y
     * @param unknown_type $c
     */
    private function drawBorder($x, $y, $c)
    {
        //bottom
        imagefilledrectangle(
            $this->image,
            $x,
            $y + $this->size,
            $x + $this->border + $this->size,
            $y + $this->border + $this->size,
            $c
        );
        //right
        imagefilledrectangle(
            $this->image,
            $x + $this->size,
            $y,
            $x + $this->border + $this->size,
            $y + $this->border + $this->size,
            $c
        );
    }


    /**
     *
     * Draw a start or checkpoint field
     * @param unknown_type $x Start coordinate of field
     * @param unknown_type $y Start coordinate of field
     * @param unknown_type $c1 Primary Color
     * @param unknown_type $c2 Secundary Color
     */
    private function drawFlagField($x, $y, $c1, $c2)
    {
        //fill bg color
        //imagefilledrectangle($this->image, $x, $y, $x+$this->size, $y+$this->size, $c2);
        imagefilledrectangle($this->image, $x, $y, $x + $this->size - 1, $y + $this->size - 1, $c2);

        $factor = $this->size / 4;
        $sende = $this->size / $factor;
        for ($m = 0; $m < $sende; $m++) {
            for ($n = 0; $n < $sende; $n++) {
                if (($m + $n) % 2 == 1) {
                    ImageFilledRectangle(
                        $this->image,
                        $x + $m * $factor,
                        $y + $n * $factor,
                        $x + $m * $factor + $factor - 1,
                        $y + $n * $factor + $factor - 1,
                        $c1
                    );
                    //echo "ImageFilledRectangle($this->image,$x+$m*$factor,$y+$n*$factor,$x+$m*$factor+$factor-1,$y+$n*$factor+$factor-1,$c1)<br>";
                }
            }
        }
        $this->drawBorder($x, $y, $this->colors['roadspecle']);
    }

    private function drawStartField($x, $y, $c1, $c2)
    {
        //bg
        imagefilledrectangle($this->image, $x, $y, $x + $this->size, $y + $this->size, $c2);

        //fg square
        imagerectangle(
            $this->image,
            $x + 0.3 * $this->size,
            $y + 0.3 * $this->size,
            $x + 0.7 * $this->size,
            $y + 0.7 * $this->size,
            $c1
        );

        //add border
        $this->drawBorder($x, $y, $this->colors['roadspecle']);
    }

    public function isCached()
    {
        return $this->fileExists();
    }

    private function fileExists()
    {
        $fn = $this->getFilename();

        return (file_exists($fn));
    }

    private function getFilestream()
    {
        return file_get_contents($this->getFilename());
    }

    public function getImage()
    {
        //if not file exists, render file
        //if (1==1)
        if (!($this->fileExists())) {
            $this->render();
        }

        //return file stream
        return $this->getFilestream();
    }
}
