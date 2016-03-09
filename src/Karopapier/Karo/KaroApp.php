<?php
/**
 * Created by PhpStorm.
 * User: pdiet
 * Date: 02.08.2015
 * Time: 21:35
 */

namespace Karopapier\Karo;

use Karopapier\Karo\KaroQuery;
use Karopapier\Karo\Model\User;


class KaroApp
{
    /** @var  $db \Karopapier\Karo\KaroQuery */
    public $db;
    /** @var  $authUser \Karopapier\Karo\Model\User */
    public $authUser;

    public function __construct()
    {
        global $remote_addr;
        $this->db = new KaroQuery();
        $this->authUser = NULL;

        if ((!isset($_COOKIE["KaroSession"])) || ($_COOKIE["KaroSession"] == "")) {
            #system("echo 'NEWID' >>dooflog");
            $rand1 = rand(100000, 900000);
            $rand2 = rand(100000, 900000);
            $session_id = md5($rand1 . md5($remote_addr) . "more" . $rand2);
            session_id($session_id);
        }

        session_name("KaroSession");
        session_start();
    }

    /**
     * Check cookie or session if user is logged in
     * @return bool
     */
    public function identify()
    {
        if (isset($_SESSION['authUser'])) {
            //$this->authUser = $_SESSION['authUser'];
            //return true;
        }

        if (isset($_COOKIE["KaroKeks"])) {

            $karocookie = $_COOKIE["KaroKeks"];
            $karoraw = base64_decode($karocookie);
            list($uid, $md5passwd) = explode("|--|", $karoraw);
            $this->authUser = $this->db->getUserByIdAndMd5($uid, $md5passwd);

            if ($this->authUser) {
                $_SESSION["authUser"] = $this->authUser;

                //assume user has been off until session timed out and this is the first visit again, so change lastvisit to last known currentvisit
                $sql = "update karo_user SET Warned=0,lastvisit=currentvisit,reallastvisit=currentvisit WHERE U_ID=:uid";
                $this->db->doQuery($sql, array("uid" => $this->authUser->getId()));
                return true;
            }
        }
        return false;
    }

    public function setCredentials($user)
    {
        global $homeurl;
        $uid = $user->getId();
        $year = 60 * 60 * 24 * 30 * 12;
        $clearpass = $user->Passwd;
        $md5pass = md5($clearpass);
        $karocoded = base64_encode($uid . "|--|" . $md5pass);
        setcookie("KaroKeks", $karocoded, time() + $year, "", $homeurl, 0);
    }

    public function fillOldVars($user)
    {
        global $username;
        global $uid;

        $props = array("Login", "U_ID", "Vorname", "Nachname", "Size", "Border", "View", "GamesPerPage", "games_order", "faulpelz", "move_autoforward", "economode", "draw_limit");
        foreach ($props as $p) {
            $k = "S_" . strtolower($p);
            $_SESSION[$k] = $user->$p;
        }
        $username = $user->getLogin();
        $uid = $user->getId();
    }

    public function updateVisits($user)
    {
        #currentvisit aktualisieren
        $sql = "update karo_user SET currentvisit=now(),Active=1,Browser=:ua WHERE U_ID=:uid";
        $this->db->doQuery($sql, array("uid" => $this->authUser->getId(), "ua" => $_SERVER['HTTP_USER_AGENT']));

        $sql = "insert into karo_visits (U_ID,visitdate) VALUES(" . $user->getId() . ",now()) on duplicate key update visitdate=now()";
        $this->db->doQuery($sql);
    }

}