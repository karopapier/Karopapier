<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 18.07.2016
 * Time: 23:57
 */

namespace AppBundle\Twig;


class CachebustExtension extends \Twig_Extension implements \Twig_ExtensionInterface
{
    private $manifest;

    public function __construct($manifest, $env)
    {
        $this->manifest = $manifest;
        $this->env = $env;
        $this->refreshed = false;
    }

    public function getFunctions()
    {
        return array(
            'getCachebustPath' => new \Twig_SimpleFunction("cachebust", [$this, 'getCachebustPath']),
        );
    }

    public function getCachebustPath($key)
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

        return $key;
    }

    public function refresh()
    {
        $manifestpath = __DIR__.'/../../../web/cachebust.json';
        $this->manifest = json_decode(file_get_contents($manifestpath), true);
        $this->refreshed = true;
    }
}