<?php
/**
 * Created by PhpStorm.
 * User: pdietrich
 * Date: 19.04.2016
 * Time: 08:25.
 */

namespace Turted\Bundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class TurtedExtension extends ConfigurableExtension
{
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        if (true === $mergedConfig['enabled']) {
            $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
            //$loader->load($mergedConfig["push"]["type"] . '_service.yml');
            $loader->load('rest_service.yml');

            $container->setParameter('turted', $mergedConfig);
        }
    }
}
