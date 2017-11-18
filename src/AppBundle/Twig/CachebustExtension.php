<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 18.07.2016
 * Time: 23:57
 */

namespace AppBundle\Twig;


class CachebustExtension extends \Twig_Extension
{
    private $manifest;

    public function __construct($webpath)
    {
        $this->manifest = json_decode(file_get_contents($webpath.'/cachebust.json'), true);
    }

    public function getFunctions()
    {
        return array(
            'getCachebustPath' => new \Twig_SimpleFunction("cachebust", array($this, 'getCachebustPath')),
        );
    }

    public function getCachebustPath($file)
    {
        if (isset($this->manifest[$file])) {
            return $this->manifest[$file];
        }

        return $$file;
    }
}