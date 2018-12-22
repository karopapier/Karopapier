<?php
/**
 * Created by PhpStorm.
 * User: monti
 * Date: 21.12.2018
 * Time: 00:18
 */

namespace AppBundle\Services;


use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Routing\RouteCollection;

class ModulesRoutesLoader extends Loader
{
    private $moduleDir;

    public function __construct($moduleDir)
    {
        $this->moduleDir = $moduleDir;
    }

    public function load($resource, $type = null)
    {
        $routes = new RouteCollection();

        $finder = new Finder();
        $finder->files()->in($this->moduleDir)->name('*Controller.php');
        foreach ($finder as $file) {
            $importedRoutes = $this->import($file->getPath(), 'annotation');
            $routes->addCollection($importedRoutes);
        }

        return $routes;
    }

    /**
     * Returns whether this class supports the given resource.
     *
     * @param mixed $resource A resource
     * @param string|null $type The resource type or null if unknown
     *
     * @return bool True if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null)
    {
        return 'modules' === $type;
    }
}