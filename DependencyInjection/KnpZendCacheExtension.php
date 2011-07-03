<?php

namespace Knp\Bundle\ZendCacheBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

/*
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * KnpZendCacheExtension is an extension for the Zend\Cache\Manager Framework library.
 */
class KnpZendCacheExtension extends Extension
{
    /**
     * Loads the cache manager configuration.
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('manager.xml');

        $templates = array();
        foreach ($configs as $config) {
            $templates = array_merge($templates, $config['templates']);
        }

        foreach($templates as $name => $template) {
            $container->findDefinition('knp_zend_cache.manager')->addMethodCall('setCacheTemplate', array($name, $template));
        }
    }
}
