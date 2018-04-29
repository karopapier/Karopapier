<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 16.04.2018
 * Time: 12:55
 */


$file = __DIR__."/100x100_php.png";

$img = imagecreate(100, 100);
$black = imagecolorallocate($img, 0, 0, 0);
$white = imagecolorallocate($img, 255, 255, 255);

for ($i = 0; $i <= 1000; $i++) {
    $x = mt_rand(0, 100);
    $y = mt_rand(0, 100);

    imagesetpixel($img, $x, $y, $white);
}
imagepng($img, $file);