<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 27.10.2017
 * Time: 14:04
 */

namespace AppBundle\Services;


class ConfigService
{
    public function __construct($config)
    {
        $this->config = $config;
    }

    public function get($key)
    {
        if (isset($this->config[$key])) {
            return $this->config[$key];
        }
    }
}