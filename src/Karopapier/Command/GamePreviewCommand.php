<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 09.03.2016
 * Time: 23:08
 */

namespace Karopapier\Command;

use Karopapier\Karo\Model\Map;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;


class GamePreviewCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
                ->setName('Karopapier:gamePreview')
                ->setDescription('Create a games preview image')
                ->addArgument(
                        'gid',
                        InputArgument::REQUIRED,
                        'What is the GameId?'
                )
                ->addArgument(
                        'folder',
                        InputArgument::REQUIRED,
                        'What is the target folder of the preview image'
                );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fs = new Filesystem();
        $gid = $input->getArgument('gid');
        $folder = $input->getArgument('folder');
        $logger = $this->getContainer()->get("logger");


        if (!$fs->isAbsolutePath($folder)) {
            $folder = getcwd() . "/" . $folder;
        }
        if ($fs->exists($folder)) {
            $logger->info("I'll create an image of " . $gid . ' in ' . $folder);
        } else {
            $logger->error("Could not find folder " . $folder);
            return false;
        }

        $data = json_decode(file_get_contents("http://www.karopapier.de/api/game/" . $gid . "/details.json"), true);
        $gamedata = $data['game'];
        $checkpoints = $gamedata['cps'];
        $mapdata = $data['map'];
        $cols = $mapdata['cols'];
        $rows = $mapdata['rows'];
        $mapcode = $mapdata['mapcode'];

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
        for ($i = 0; $i < strlen($mapcode); $i++) {
            $f = $mapcode[$i];
            if ($f == "\r") continue;
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
            if (($player['position'] > 0) && ($player['status'] == "ok")) {
                $mcount--;
            }
            if ($mcount < 1) continue;
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

        imagepng($img, $folder . "/" . $gid . ".png");
        return true;
    }
}