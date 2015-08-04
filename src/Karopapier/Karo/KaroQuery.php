<?php
/**
 * Created by PhpStorm.
 * User: pdiet
 * Date: 02.08.2015
 * Time: 21:00
 */

namespace Karopapier\Karo;

use Karopapier\Karo\Model\User;

class KaroQuery
{
    private $pdo;

    public function __construct()
    {
        global $db_host;
        global $db_name;
        global $db_user;
        global $db_pwd;
        try {
            $this->pdo = new \PDO('mysql:host=' . $db_host . ';dbname=' . $db_name, $db_user, $db_pwd);
            $this->pdo->query("set names utf8");
        } catch (PDOException $e) {
            die("Database connection error.<br>\n");
        }
    }

    public function getUserByIdAndMd5($id, $md5)
    {
        $sql = "select * from karo_user where U_ID=:id and md5(passwd)=:passwd";
        $stmt = $this->doQuery($sql, array("id" => $id, "passwd" => $md5));
        if ($stmt->rowCount() != 1) {
            return null;
        }
        $user = new User();
        $stmt->setFetchMode(\PDO::FETCH_INTO, $user);
        $user = $stmt->fetch(\PDO::FETCH_INTO);
        return $user;

    }

    public function getUserById($id)
    {
        $sql = "select * from karo_user where U_ID=:id";
        $stmt = $this->doQuery($sql, array("id" => $id));
        if ($stmt->rowCount() != 1) {
            return null;
        }
        $user = new User();
        $stmt->setFetchMode(\PDO::FETCH_INTO, $user);
        $user = $stmt->fetch(\PDO::FETCH_INTO);
        return $user;
    }

    /**
     * @param $login
     * @param $passwd
     * @return \Karopapier\Karo\Model\User|mixed|null
     * @throws \Exception
     */
    public function getUserByLoginPassword($login, $passwd)
    {
        $sql = "select * from karo_user where login=:login and passwd=:passwd";
        $stmt = $this->doQuery($sql, array("login" => $login, "passwd" => $passwd));
        if ($stmt->rowCount() != 1) {
            return null;
        }
        $user = new User();
        $stmt->setFetchMode(\PDO::FETCH_INTO, $user);
        $user = $stmt->fetch(\PDO::FETCH_INTO);
        return $user;
    }

    /**
     * @return \PDOStatement
     * @throws \Exception
     */
    public function doQuery($sql)
    {
        $vars = array();
        if (func_num_args() > 1) {
            $vars = func_get_arg(1);
        };

        $stmt = $this->pdo->prepare($sql);
        foreach ($vars as $name => $value) {
            //echo "Binding ".$name."=>".$value;
            $stmt->bindValue($name, $value);
        }
        if ($stmt->execute()) {
            return $stmt;
        } else {
            $errArr = $stmt->errorInfo();
            throw new \Exception($errArr[2]);
        }
    }

    public function dlookup($col, $tab, $cond)
    {
        $sql = "select " . $col . " from " . $tab . " where " . $cond;
        $res = $this->doQuery($sql);
        $row = $res->fetch();
        return $row[$col];
    }
}