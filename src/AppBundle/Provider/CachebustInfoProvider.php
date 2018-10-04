<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 19.07.2018
 * Time: 15:28
 */

namespace AppBundle\Provider;


class CachebustInfoProvider
{
    public function __construct($manifest, $env)
    {
        $this->manifest = $manifest;
        $this->env = $env;
        $this->refreshed = false;
    }

    public function get($key)
    {
        // Da symfony aenderungen nicht mitbekommt, machen wir in allen Nicht-Prod-Environments hier ein refresh
        if ($this->env !== 'prod') {
            if (!$this->refreshed) {
                $this->refresh();
            }
        }

        if (array_key_exists($key, $this->manifest)) {
            return $this->manifest[$key];
        }

        return '';
    }

    public function refresh()
    {
        $manifestpath = __DIR__.'/../../../public/spa/cachebust.json';
        $this->manifest = json_decode(file_get_contents($manifestpath), true);
        $this->refreshed = true;
    }
}