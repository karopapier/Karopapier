<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 09.03.2016
 * Time: 23:50
 */

namespace Karopapier\Karo\Model;

class Map
{

    public static $FIELDS = array(
            "F" => "finish",
            "O" => "road",
            "P" => "parc",
            "S" => "start",
            "G" => "gold",
            "L" => "lava",
            "N" => "snow",
            "T" => "tar",
            "V" => "mountain",
            "W" => "water",
            "X" => "grass",
            "Y" => "sand",
            "Z" => "mud",
            "." => "night",
            "1" => "cp1",
            "2" => "cp2",
            "3" => "cp3",
            "4" => "cp4",
            "5" => "cp5",
            "6" => "cp6",
            "7" => "cp7",
            "8" => "cp8",
            "9" => "cp9"
    );

    public static $COLORS = array(
            "road" => "128,128,128",
            "road_2" => "100,100,100",
            "grass" => "0,200,0",
            "grass_2" => "0,180,0",
            "sand" => "230,230,115",
            "sand_2" => "200,200,100",
            "mud" => "100,70,0",
            "mud_2" => "90,60,0",
            "mountain" => "100,100,100",
            "mountain_2" => "0,0,0",
            "gold" => "255, 201, 14",
            "gold_2" => "255, 255, 0",
            "lava" => "240, 0, 0",
            "lava_2" => "180, 0, 0",
            "water" => "0,60,200",
            "water_2" => "0,30,100",
            "snow" => "255,255,255",
            "snow_2" => "120,120,120",
            "start" => "100,100,100",
            "start_2" => "200,200,200",
            "tar" => "0,0,0",
            "tar_2" => "40,40,40",
            "finish" => "255,255,255",
            "finish_2" => "0,0,0",
            "night" => "0,0,0",
            "night_2" => "0,0,0",
            "parc" => "200,200,200",
            "parc_2" => "120,120,120",
            "cp1" => "000,102,255", //Checkpoint 1
            "cp1_2" => "0,0,0",
            "cp2" => "000,100,200", //Checkpoint 2
            "cp2_2" => "255,255,255",
            "cp3" => "000,255,102", //Checkpoint 3
            "cp3_2" => "0,0,0",
            "cp4" => "000,200,000", //Checkpoint 4
            "cp4_2" => "255,255,255",
            "cp5" => "255,255,000", //Checkpoint 5
            "cp5_2" => "0,0,0",
            "cp6" => "200,200,000", //Checkpoint 6
            "cp6_2" => "255,255,255",
            "cp7" => "255,000,000", //Checkpoint 7
            "cp7_2" => "0,0,0",
            "cp8" => "200,000,000", //Checkpoint 8
            "cp8_2" => "255,255,255",
            "cp9" => "255,000,255", //Checkpoint 9
            "cp9_2" => "0,0,0"
    );

    public function getColor($f) {
        return $this::$COLORS[$this::$FIELDS[$f]];
    }

}