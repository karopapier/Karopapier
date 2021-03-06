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
use AppBundle\Repository\GameRepository;
use AppBundle\Services\ConfigService;
use Doctrine\ORM\NoResultException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\SerializerInterface;

class GameThumbnailGenerator
{
    private $folder;

    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var MapImageCache
     */
    private $mapImageCache;

    /**
     * @var GameRepository
     */
    private $gameRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ConfigService $config,
        SerializerInterface $serializer,
        MapImageCache $mapImageCache,
        GameRepository $gameRepository,
        LoggerInterface $logger
    ) {
        $this->mapImageCache = $mapImageCache;
        $this->logger = $logger;
        $this->folder = $config->get('game_thumbs_cache_dir');
        $this->serializer = $serializer;
        $this->gameRepository = $gameRepository;
    }

    /**
     * Make sure that a thumbnail exists
     * If not, generate one
     *
     * @param Game $game
     * @return bool
     */
    public function ensureByGameId($gid)
    {
        $gid = (int)$gid;
        if ($this->exists($gid)) {
            return;
        }

        $this->generateByGameId($gid);

        return false;
    }

    private function getFilepath($gid)
    {
        return $this->folder.'/'.$gid.'.png';
    }

    private function exists($gid)
    {
        return file_exists($this->getFilepath($gid));
    }

    /**
     * @param $gid
     * @throws \Exception
     */
    public function generateByGameId($gid)
    {
        $gid = (int)$gid;
        /** @var Game $game */
        try {
            $game = $this->gameRepository->findGameWithPlayers($gid);
        } catch (NoResultException $exception) {
            throw new \Exception(sprintf('Game ID %s not found', $gid));
        }

        $this->gameRepository->addMovesData($game);
        // not needed
        // $this->gameRepository->addCheckpointData($game);

        return $this->generate($game);
    }

    public function addMove($gid, $uid, $x, $y, $xv, $yv)
    {
        // wenn nicht da, komplett neu machen
        if (!$this->exists($gid)) {
            return $this->generateByGameId($gid);
        }

        // wenn da, zum Bild eine line hinzu
        $game = $this->gameRepository->findGameWithPlayers($gid);
        if (!$game) {
            return;
        }

        $players = $game->getPlayers();

        $colorHtml = '000000';
        foreach ($players as $player) {
            if ($player->getUser()->getId() === $uid) {
                $colorHtml = $player->getUser()->getColor();
                break;
            }
        }

        $img = imagecreatefrompng($this->getFilepath($gid));
        $r = hexdec(substr($colorHtml, 0, 2));
        $g = hexdec(substr($colorHtml, 2, 2));
        $b = hexdec(substr($colorHtml, 4, 2));
        $paint = imagecolorallocate($img, $r, $g, $b);
        $prevX = $x - $xv;
        $prevY = $y - $yv;
        imageline($img, $prevX, $prevY, $x, $y, $paint);
        imagepng($img, $this->folder."/".$gid.".png");
    }

    public function initWithMap($gid)
    {
        /** @var Game $game */
        $game = $this->gameRepository->find($gid);
        $map = $game->getMap();
        $thumbnail = $this->mapImageCache->getThumbnail($map);
        copy($this->mapImageCache->getFilepath($thumbnail), $this->getFilepath($gid));
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

        $map = $game->getMap();

        $thumbnail = $this->mapImageCache->getThumbnail($map);
        $src = $this->mapImageCache->getCachedFilePath($thumbnail);
        $img = imagecreatefrompng($src);

        $gameData = $this->serializer->normalize(
            $game,
            'array',
            [
                'players' => true,
                'moves' => true,
            ]
        );

        //now players and moves
        $playerdata = $gameData['players'];
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