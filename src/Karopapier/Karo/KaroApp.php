<?php
/**
 * Created by PhpStorm.
 * User: pdiet
 * Date: 02.08.2015
 * Time: 21:35
 */

namespace Karopapier\Karo;
use Karopapier\Karo\KaroQuery;


class KaroApp
{
    public $db;
    public $authUser;
    public function __construct()
    {
        $this->db = new KaroQuery();
        $this->authUser=NULL;
    }
}