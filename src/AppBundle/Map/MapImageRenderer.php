<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 03.09.2018
 * Time: 17:48
 */

namespace AppBundle\Map;

use AppBundle\Model\MapImage;
use AppBundle\Model\MapImagePalette;
use AppBundle\ValueObject\MapImageOptions;

class MapImageRenderer
{
    private $image;
    /**
     * @var MapImagePalette
     */
    private $palette;
    private $colors;
    /**
     * @var MapImageOptions
     */
    private $options;

    private $size;
    private $border;
    private $fieldsize;

    public function __construct()
    {
        $this->palette = new MapImagePalette();
    }

    public function getImageString(MapImage $mapImage)
    {
        ob_start();
        $this->render($mapImage);
        $imageString = ob_get_clean();

        return $imageString;
    }

    public function render(MapImage $mapImage)
    {
        $this->map = $mapImage->getMap();
        $this->options = $mapImage->getOptions();
        $this->initImage($mapImage);
        $this->drawFields();

        $filetype = $mapImage->getOptions()->filetype;

        if ($filetype === "png") {
            return imagepng($this->image, null, 8, PNG_NO_FILTER);
        }

        if ($filetype === "jpg") {
            return imagejpeg($this->image);
        }

        if ($filetype === "gif") {
            return imagegif($this->image);
        }
    }

    private function initImage(MapImage $mapImage)
    {
        $w = $mapImage->getImageWidth();
        $h = $mapImage->getImageHeight();
        $this->image = imagecreatetruecolor($w, $h);

        $this->size = $mapImage->getOptions()->size;
        $this->border = $mapImage->getOptions()->border;
        $this->fieldsize = ($this->size + $this->border);

        $this->colors = $this->allocateColors();

        //set bg
        // imagefilledrectangle($this->image, 0, 0, $w, $h, $this->colors['offroadspecle']);
    }

    private function allocateColors()
    {
        $palette = $this->palette->getPalette();

        $colors = [];
        foreach ($palette as $colorKey => $colorArray) {
            $colors[$colorKey] = imagecolorallocate($this->image, $colorArray[0], $colorArray[1], $colorArray[2]);
        }

        return $colors;
    }

    private function drawFields()
    {
        $matrix = $this->map->getMatrix();
        //r = row walker
        //c = column walker
        $maxC = $this->map->getNbCols();
        $maxR = $this->map->getNbRows();
        for ($r = 0; $r < $maxR; $r++) {
            for ($c = 0; $c < $maxC; $c++) {
                $this->drawCodeField($r, $c, $matrix[$r][$c]);
            }
        }
    }

    private function drawCodeField($r, $c, $char)
    {
        $nightFields = ['S', 'F', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $night = $this->options->night;
        if ($night && (!in_array($char, $nightFields))) {
            return;
        }

        $x = $c * ($this->fieldsize);
        $y = $r * ($this->fieldsize);

        $fullfields = ['L', 'N', 'V', 'W', 'X', 'Y', 'Z', 'O', 'G'];

        if (in_array($char, $fullfields)) {
            $fillColor = $this->colors[$char];
            $specleColor = $this->colors[$char.'specle'];

            return $this->drawStandardField($x, $y, $fillColor, $specleColor);
        }

        //finish
        if ($char === "F") {
            return $this->drawFlagField($x, $y, $this->colors['finish1'], $this->colors['finish2']);
        }

        //checkpoint
        if (is_numeric($char)) {
            if ($this->options->cps) {
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

            return;
        }

        //Start
        if ($char === "S") {
            return $this->drawStartField($x, $y, $this->colors['start1'], $this->colors['start2']);
        }

        //Parc ferme
        if ($char === "P") {
            // no specles
            return $this->drawStandardField($x, $y, $this->colors['parc'], $this->colors['roadspecle'], false);
        }
    }

    /**
     *
     * Draw a "default" field for road or offroad with a solid fillcolor plus random specles
     * @param unknown_type $x Start coordinate of field
     * @param unknown_type $y Start coordinate of field
     * @param unknown_type $c Fill color
     * @param unknown_type $s Specle Color
     */
    private function drawStandardField($x, $y, $c, $s, $specles = true)
    {
        //fill
        imagefilledrectangle(
            $this->image,
            $x,
            $y,
            $x + $this->fieldsize,
            $y + $this->fieldsize,
            $c
        );

        $this->drawBorder($x, $y, $s);

        if (($specles) && ($this->options->specles)) {
            //add random specles
            for ($i = 0; $i < $this->size; $i++) {
                imagesetpixel($this->image, $x + rand(0, $this->size), $y + rand(0, $this->size), $s);
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
        imagefilledrectangle($this->image, $x, $y + $this->size, $x + $this->fieldsize, $y + $this->fieldsize, $c);
        //right
        imagefilledrectangle($this->image, $x + $this->size, $y, $x + $this->fieldsize, $y + $this->fieldsize, $c);
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
}
