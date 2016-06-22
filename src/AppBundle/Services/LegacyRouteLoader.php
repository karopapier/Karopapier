<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 17.06.2016
 * Time: 12:52
 */

namespace AppBundle\Services;


//docu http://symfony.com/doc/current/cookbook/routing/custom_route_loader.html

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class LegacyRouteLoader extends Loader
{
    private $webRoot;
    private $loaded = false;
   
    public function __construct($webRoot)
    {
        $this->webRoot = $webRoot;
    }

    /**
     * @param mixed $resource
     * @param null $type
     * @return RouteCollection
     */
    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add the "extra" loader twice');
        }

        $this->loaded = true;
        return $this->getLegacyRoutes();
    }

    /**
     * stolen here: https://slidr.io/derrabus/modernisieren-mit-symfony#16
     *
     * @return RouteCollection
     */
    public function getLegacyRoutes()
    {
        $finder = new Finder();
        $finder->files()->name('*.php');
        $finder->depth('== 0');
        $collection = new RouteCollection();
        /** @var SplFileInfo $file */
        foreach ($finder->in($this->webRoot) as $file) {
            $collection->add('karo.legacy.' . str_replace('/', '__', substr($file->getRelativePathname(), 0, -4)),
                    new Route($file->getRelativePathname(),
                            [
                                    'legacyScript' => $file->getPathname(),
                                    'requestPath' => '/' . $file->getRelativePathname()
                            ]
                    )
            );
        }
        return $collection;
    }

    public function supports($resource, $type = null)
    {
        return 'phpfiles' === $type;
    }
}