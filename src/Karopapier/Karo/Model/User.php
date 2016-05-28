<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 03.08.2015
 * Time: 17:28
 */

namespace Karopapier\Karo\Model;

class User
{
    public function getId()
    {
        return $this->U_ID;
    }

    public function getName()
    {
        return $this->login;
    }

    public function getLogin()
    {
        return $this->login;
    }
    
    public function hasRight($permission)
    {
        global $app;
        $sql = "select value from karo_admin join karo_rights on karo_admin.R_ID = karo_rights.R_ID WHERE karo_rights.Permission = :p";

        $res = $app->db->doQuery($sql, array("p" => $permission));
        $right = $res->fetchColumn(0);
        return $right;
    }
}