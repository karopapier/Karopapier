<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 04.09.2018
 * Time: 12:44
 */

namespace AppBundle\Game;


use AppBundle\Entity\Game;
use AppBundle\Map\MapImageCache;
use AppBundle\Services\ConfigService;
use Karopapier\Karo\Model\Map;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

class GameThumbnailGenerator
{
    /**
     * @var MapImageCache
     */
    private $mapImageCache;
    /**
     * @var LoggerInterface
     */
    private $logger;
    private $folder;

    public function __construct(ConfigService $config, MapImageCache $mapImageCache, LoggerInterface $logger)
    {
        $this->mapImageCache = $mapImageCache;
        $this->logger = $logger;
        $this->folder = $config->get('game_thumbs_cache_dir');
    }

    public function generate(Game $game)
    {
        $this->logger->debug('Ich mach mal ein Thumbnail für Game '.$game->getName());

        $gid = $game->getId();
        $fs = new Filesystem();
        $folder = $this->folder;
        $logger = $this->logger;


        if (!$fs->isAbsolutePath($folder)) {
            $folder = getcwd()."/".$folder;
        }
        if ($fs->exists($folder)) {
            $logger->info("I'll create an image of ".$gid.' in '.$folder);
        } else {
            $logger->error("Could not find folder ".$folder);

            return false;
        }

        $data = json_decode(file_get_contents("http://www.karopapier.de/api/game/".$gid."/details.json"), true);
        $checkpoints = $game->getCheckpointsEnabled();
        $map = $game->getMap();
        $cols = $map->getNbCols();
        $rows = $map->getNbRows();
        $mapcode = $map->getCode();

        $map = new Map();

        //allocated all colors
        $img = imagecreate($cols, $rows);
        $colors = array();
        foreach (array_keys($map::$FIELDS) as $f) {
            //allocate road for checkpoints if not enabled
            if (is_numeric($f) && (!$checkpoints)) {
                $colors[$f] = $colors['O'];
            } else {
                list($r, $g, $b) = explode(',', $map->getColor($f));
                $colors[$f] = imagecolorallocate($img, $r, $g, $b);
            }
        }

        $row = 0;
        $col = 0;
        $l = strlen($mapcode);
        for ($i = 0; $i < $l; $i++) {
            $f = $mapcode[$i];
            if ($f == "\r") {
                continue;
            }
            if ($f == "\n") {
                $row++;
                $col = 0;
                continue;
            }
            imagesetpixel($img, $col, $row, $colors[$f]);
            $col++;
        }

        //now players and moves
        $playerdata = $data['players'];
        $pcount = count($playerdata);

        for ($p = 0; $p < $pcount; $p++) {
            $player = $playerdata[$p];
            $colorHtml = $player['color'];
            $r = hexdec(substr($colorHtml, 0, 2));
            $g = hexdec(substr($colorHtml, 2, 2));
            $b = hexdec(substr($colorHtml, 4, 2));
            $color = imagecolorallocate($img, $r, $g, $b);

            $moves = $player['moves'];
            $mcount = count($moves);
            //if finished, don't draw last line
            #if (($player['position'] > 0) && ($player['status'] == "ok")) {
            if ($player['position'] > 0) {
                $mcount--;
            }
            if ($mcount < 1) {
                continue;
            }
            $move = $moves[0];
            $x = $move['x'];
            $y = $move['y'];
            imagesetpixel($img, $x, $y, $color);
            for ($m = 1; $m < $mcount; $m++) {
                $move = $moves[$m];
                $nx = $move['x'];
                $ny = $move['y'];
                imageline($img, $x, $y, $nx, $ny, $color);
                $x = $nx;
                $y = $ny;
            }
        }

        imagepng($img, $folder."/".$gid.".png");

        return true;
    }
}