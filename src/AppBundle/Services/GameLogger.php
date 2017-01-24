<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 12.06.2016
 * Time: 12:49
 */

namespace AppBundle\Services;

class GameLogger
{
    private $logpath;

    public function __construct($logpath)
    {
        $this->logpath = $logpath;
    }

    public function log($gid, $text)
    {
        $f = fopen($this->logpath . "/" . $gid . ".log", "a");
        if (!$f) {
            throw new \Exception("GAME_LOGFILE_ERROR");
        }
        $now = date('Y-m-d H:i:s');
        fwrite($f, "$now: $text\n");
        fclose($f);
    }
}