<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 18.07.2016
 * Time: 23:57
 */

namespace AppBundle\Twig;


use AppBundle\Services\ConfigService;

class AssetExtension extends \Twig_Extension
{
    /**
     * @var ConfigService
     */
    private $config;

    public function __construct(ConfigService $config)
    {
        $this->config = $config;
    }

    public function getInitialCss()
    {
        $projectDir = $this->config->get('project_dir');
        $initial = file_get_contents($projectDir.'/web/css/initial.css');

        return $initial;
    }

    public function getFunctions()
    {
        return array(
            'getInitialCss' => new \Twig_SimpleFunction("getInitialCss", array($this, "getInitialCss")),
        );
    }
}