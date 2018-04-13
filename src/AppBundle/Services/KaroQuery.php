<?php
/**
 * Created by PhpStorm.
 * User: pdiet
 * Date: 02.08.2015
 * Time: 21:00
 */

namespace AppBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use PDOException;


class KaroQuery
{
    /** @var \Doctrine\DBAL\Driver\Connection */
    private $pdo;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
        try {
            $this->pdo = $manager->getConnection()->getWrappedConnection();
            $this->pdo->query("set names utf8");
        } catch (PDOException $e) {
            die("Database connection error.<br>\n");
        }
    }

    /**
     * @return \Doctrine\DBAL\Driver\Statement
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
        }

        $errArr = $stmt->errorInfo();
        throw new \Exception($errArr[2]);
    }

    public function dlookup($col, $tab, $cond)
    {
        $sql = "Select ".$col." from ".$tab." where ".$cond;
        $res = $this->doQuery($sql);

        return $res->fetchColumn();
    }
}