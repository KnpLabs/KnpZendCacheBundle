Integrates Zend Cache framework into Symfony2.

## What it does

This bundle allows to configure a Zend\Cache\Manager, and instanciate it, from the D.I.C.
It contains no caching logic. It does **not** extend nor wrap Zend Cache classes.
All it does is configure the service container to ease cache configuration and usage.

[Learn more about Zend Cache framework](http://framework.zend.com/manual/en/zend.cache.html).

## Installation

### Add ZendCacheBundle to your src/Bundle dir

    git submodule add git://github.com/knplabs/ZendCacheBundle.git src/Bundle/ZendCacheBundle

### Add ZendCacheBundle to your application kernel

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Bundle\ZendCacheBundle\ZendCacheBundle(),
            // ...
        );
    }

### Add Zend Framework to the include path

ZF2 fails to load some classes properly. We need to add ZF2 path to PHP include path:

    // src/autoload.php
    set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__.'/vendor/zend/library');

### Configure your cache templates

This is an example of configuration. Two templates cache are declared.

    # app/config.yml
    zend_cache.config:
        templates:
            my_template_cache:
                frontend:
                    name: Core
                    options:
                        lifetime: 7200
                        automatic_serialization: true
                backend:
                    name: File
                    options:
                        cache_dir: %kernel.root_dir%/cache/%kernel.environment%/zend
            another_cache:
                frontend:
                    name: Function
                    options:
                        lifetime: 3600
                        cache_by_default: false
                backend:
                    name: Apc
## Usage

Get the cache you declared from the service container:

    $cache = $container->get('zend.cache_manager')->getCache('my_template_cache');

    $cache->save($data, 'identifier_string');

    $data = $cache->load('identifier_string');

From a controller, you can use a simplified syntax:

    $cache = $this['zend.cache_manager']->getCache('my_template_cache');

As it is just pure Zend cache, please refer to [Zend Cache documentation](http://framework.zend.com/manual/en/zend.cache.introduction.html).

## Useful links

See [how to declare cache templates](http://framework.zend.com/manual/en/zend.cache.cache.manager.html), available [cache frontends](http://framework.zend.com/manual/en/zend.cache.frontends.html) and [cache backends](http://framework.zend.com/manual/en/zend.cache.backends.html).
