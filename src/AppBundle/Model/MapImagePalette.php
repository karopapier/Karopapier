<?php

namespace AppBundle\Model;

class MapImagePalette
{
    public function __construct()
    {

        $this->palette = array(
            'road' => Array(128, 128, 128),
            'roadspecle' => Array(100, 100, 100),
            'offroad' => Array(0, 200, 0),
            #'offroad'=>Array(255,255,255), #snow
            'offroadspecle' => Array(0, 180, 0),
            #'offroadspecle'=>Array(200,200,200), #snow
            'offroadsand' => Array(230, 230, 115),
            'offroadsandspecle' => Array(200, 200, 100),
            'offroadmud' => Array(100, 70, 0),
            'offroadmudspecle' => Array(90, 60, 0),
            'mountain' => Array(100, 100, 100),
            'mountainspecle' => Array(0, 0, 0),
            'gold' => Array(255, 201, 14),
            'goldspecle' => Array(255, 255, 0),
            'lava' => Array(240, 0, 0),
            'lavaspecle' => Array(180, 0, 0),
            'water' => Array(0, 60, 200),
            'waterspecle' => Array(0, 30, 100),
            'snow' => Array(255, 255, 255),
            'snowspecle' => Array(120, 120, 120),
            'tar' => Array(0, 0, 0),
            'tarspecle' => Array(40, 40, 40),
            'start1' => Array(200, 200, 200),
            'start2' => Array(100, 100, 100),
            'finish1' => Array(255, 255, 255),
            'finish2' => Array(0, 0, 0),
            'checkpoint1' => Array(0, 102, 255), #Checkpoint 1
            'checkpoint2' => Array(0, 100, 200), #Checkpoint 2
            'checkpoint3' => Array(0, 255, 102), #Checkpoint 3
            'checkpoint4' => Array(0, 200, 0), #Checkpoint 4
            'checkpoint5' => Array(255, 255, 0), #Checkpoint 5
            'checkpoint6' => Array(200, 200, 0), #Checkpoint 6
            'checkpoint7' => Array(255, 0, 0), #Checkpoint 7
            'checkpoint8' => Array(200, 0, 0), #Checkpoint 8
            'checkpoint9' => Array(255, 0, 255), #Checkpoint 9
            'checkpointBgOdd' => Array(0, 0, 0),
            'checkpointBgEven' => Array(255, 255, 255),
            'fog' => Array(0, 0, 0),
            'parc' => Array(200, 200, 200),
        );

        //easy char defaults:
        $this->palette['X'] = $this->palette['offroad'];
        $this->palette['Xspecle'] = $this->palette['offroadspecle'];
        $this->palette['Y'] = $this->palette['offroadsand'];
        $this->palette['Yspecle'] = $this->palette['offroadsandspecle'];
        $this->palette['Z'] = $this->palette['offroadmud'];
        $this->palette['Zspecle'] = $this->palette['offroadmudspecle'];
        $this->palette['O'] = $this->palette['road'];
        $this->palette['Ospecle'] = $this->palette['roadspecle'];
        $this->palette['L'] = $this->palette['lava'];
        $this->palette['Lspecle'] = $this->palette['lavaspecle'];
        $this->palette['N'] = $this->palette['snow'];
        $this->palette['Nspecle'] = $this->palette['snowspecle'];
        $this->palette['G'] = $this->palette['gold'];
        $this->palette['Gspecle'] = $this->palette['goldspecle'];
        $this->palette['V'] = $this->palette['mountain'];
        $this->palette['Vspecle'] = $this->palette['mountainspecle'];
        $this->palette['W'] = $this->palette['water'];
        $this->palette['Wspecle'] = $this->palette['waterspecle'];
        $this->palette['T'] = $this->palette['tar'];
        $this->palette['Tspecle'] = $this->palette['tarspecle'];
    }
}
