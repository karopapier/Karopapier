<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 27.10.2017
 * Time: 14:04
 */

namespace AppBundle\Services;

class ConfigService extends \Twig_Extension
{
    private $config = [];

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function get($key)
    {
        $a = $this->config;
        $dots = explode('.', $key);
        foreach ($dots as $dot) {
            if (!(isset($a[$dot]))) {
                throw new \Exception('Config value '.$key.' not found');
            }
            $a = $a[$dot];
        }

        return $a;
    }

    public function getFunctions()
    {
        return [
            "getConfig" => new \Twig_SimpleFunction("getConfig", [$this, "get"]),

        ];
    }
}