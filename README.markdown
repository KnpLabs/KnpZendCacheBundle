Integrates Zend Cache framework into Symfony2.

## What it does

This bundle allows to configure a Zend\Cache\Manager, and instanciate it, from the D.I.C.
[Learn more about Zend Cache framework](http://framework.zend.com/manual/en/zend.cache.html)

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

### Configure your cache templates

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
                    name: Core
                    options:
                        cache_dir: %kernel.root_dir%/cache/zend
            another_cache:
                frontend:
                    name: Function
                    options:
                        lifetime: 3600
                        cache_by_default: false
                backend:
                    name: Apc

## Useful links

See [how to declare cache templates](http://framework.zend.com/manual/en/zend.cache.cache.manager.html), available [cache frontends](http://framework.zend.com/manual/en/zend.cache.frontends.html) and [cache backends](http://framework.zend.com/manual/en/zend.cache.backends.html).
