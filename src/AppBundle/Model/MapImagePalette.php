<?php

namespace AppBundle\Model;

class MapImagePalette
{
    public function getPalette()
    {
        $palette = array(
            'road' => [128, 128, 128],
            'roadspecle' => [100, 100, 100],
            'offroad' => [0, 200, 0],
            #'offroad'=>Array(255,255,255), #snow
            'offroadspecle' => [0, 180, 0],
            #'offroadspecle'=>Array(200,200,200), #snow
            'offroadsand' => [230, 230, 115],
            'offroadsandspecle' => [200, 200, 100],
            'offroadmud' => [100, 70, 0],
            'offroadmudspecle' => [90, 60, 0],
            'mountain' => [100, 100, 100],
            'mountainspecle' => [0, 0, 0],
            'gold' => [255, 201, 14],
            'goldspecle' => [255, 255, 0],
            'lava' => [240, 0, 0],
            'lavaspecle' => [180, 0, 0],
            'water' => [0, 60, 200],
            'waterspecle' => [0, 30, 100],
            'snow' => [255, 255, 255],
            'snowspecle' => [120, 120, 120],
            'tar' => [0, 0, 0],
            'tarspecle' => [40, 40, 40],
            'start1' => [200, 200, 200],
            'start2' => [100, 100, 100],
            'finish1' => [255, 255, 255],
            'finish2' => [0, 0, 0],
            'checkpoint1' => [0, 102, 255], #Checkpoint 1
            'checkpoint2' => [0, 100, 200], #Checkpoint 2
            'checkpoint3' => [0, 255, 102], #Checkpoint 3
            'checkpoint4' => [0, 200, 0], #Checkpoint 4
            'checkpoint5' => [255, 255, 0], #Checkpoint 5
            'checkpoint6' => [200, 200, 0], #Checkpoint 6
            'checkpoint7' => [255, 0, 0], #Checkpoint 7
            'checkpoint8' => [200, 0, 0], #Checkpoint 8
            'checkpoint9' => [255, 0, 255], #Checkpoint 9
            'checkpointBgOdd' => [0, 0, 0],
            'checkpointBgEven' => [255, 255, 255],
            'fog' => [0, 0, 0],
            'parc' => [200, 200, 200],
        );

        //easy char defaults:
        $palette['X'] = $palette['offroad'];
        $palette['Xspecle'] = $palette['offroadspecle'];
        $palette['Y'] = $palette['offroadsand'];
        $palette['Yspecle'] = $palette['offroadsandspecle'];
        $palette['Z'] = $palette['offroadmud'];
        $palette['Zspecle'] = $palette['offroadmudspecle'];
        $palette['O'] = $palette['road'];
        $palette['Ospecle'] = $palette['roadspecle'];
        $palette['L'] = $palette['lava'];
        $palette['Lspecle'] = $palette['lavaspecle'];
        $palette['N'] = $palette['snow'];
        $palette['Nspecle'] = $palette['snowspecle'];
        $palette['G'] = $palette['gold'];
        $palette['Gspecle'] = $palette['goldspecle'];
        $palette['V'] = $palette['mountain'];
        $palette['Vspecle'] = $palette['mountainspecle'];
        $palette['W'] = $palette['water'];
        $palette['Wspecle'] = $palette['waterspecle'];
        $palette['T'] = $palette['tar'];
        $palette['Tspecle'] = $palette['tarspecle'];

        return $palette;
    }
}
