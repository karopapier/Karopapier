<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 12.06.2016
 * Time: 12:49
 */

namespace AppBundle\Game;

use AppBundle\Services\ConfigService;

class GameLogger
{
    private $logpath;

    public function __construct(ConfigService $configService)
    {
        $this->logpath = $configService->get('game_logs_dir');
    }

    public function log($gid, $text)
    {
        $f = fopen($this->logpath."/".$gid.".log", "a");
        if (!$f) {
            throw new \Exception("GAME_LOGFILE_ERROR");
        }
        $now = date('Y-m-d H:i:s');
        fwrite($f, "$now: $text\n");
        fclose($f);
    }
}