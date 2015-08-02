<?php
/**
 * Created by PhpStorm.
 * User: pdiet
 * Date: 02.08.2015
 * Time: 21:00
 */

namespace Karopapier\Karo;

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

    /**
     * @return \PDOStatement
     * @throws \Exception
     */
    public function doQuery($sql)
    {
        $vars=array();
        if (func_num_args() >1) {
            $vars = func_get_arg(1);
        };

        $stmt = $this->pdo->prepare($sql);
        foreach ($vars as $name=>$value) {
            $stmt->bindValue($name, $value);
        }
        if ($stmt->execute()) {
            return $stmt;
        } else {
            throw new \Exception("HILFE");
        }
    }

    public function dlookup($col, $tab, $cond)
    {
        $sql = "select ". $col . " from " . $tab . " where " . $cond;
        $res = $this->doQuery($sql);
        $row = $res->fetch();
        return $row[$col];
    }
}