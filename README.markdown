# KnpZendCacheBundle

In Symfony2 you can use [HTTP cache](http://symfony.com/doc/2.0/book/http_cache.html).
That's great to cache a page or part of the page.

But what if you want to cache a variable? That's where you should use KnpZendCacheBundle.

To avoid code duplication we use the well-known [Zend Cache](http://framework.zend.com/manual/en/zend.cache.html) component in our Symfony2 application.  
It works great and already has all sort of options - should you need it.

## Behind the scene

This bundle allows to configure a `Zend\Cache\Manager`, and instanciate it, from the DIC.  
It does not contain any caching logic: that's [Zend Cache](http://framework.zend.com/manual/en/zend.cache.html)'s role.

So you should read the [Zend Cache documentation](http://framework.zend.com/manual/en/zend.cache.introduction.html)
if you need anything of the ordinary.

See [how to declare cache templates](http://framework.zend.com/manual/en/zend.cache.cache.manager.html), available [cache frontends](http://framework.zend.com/manual/en/zend.cache.frontends.html) and [cache backends](http://framework.zend.com/manual/en/zend.cache.backends.html).

## Installation

### Download KnpZendCacheBundle in vendor/bundles/Knp/Bundle/ZendCacheBundle dir

If you use git:

    git submodule add http://github.com/KnpLabs/KnpZendCacheBundle.git vendor/bundles/Knp/Bundle/ZendCacheBundle

### Download zend-cache to your vendor/Zend/Cache dir

If you use git:

    git submodule add http://github.com/KnpLabs/zend-cache.git vendor/Zend/Cache
    git submodule add http://github.com/KnpLabs/zend-filter.git vendor/Zend/Filter

### Add the new namespaces to your autoloader

```php
<?php
// app/autoload.php
$loader->registerNamespaces(array(
    'Knp'                       => __DIR__.'/../vendor/bundles',
    'Zend'                      => __DIR__.'/../vendor',
    // ...
));
```

### Add KnpZendCacheBundle to your application kernel

```php
<?php
// app/AppKernel.php
public function registerBundles()
{
    return array(
        // ...
        new Knp\Bundle\ZendCacheBundle\KnpZendCacheBundle(),
        // ...
    );
}
```

## Things you should know about Zend Cache

### Frontends: what do you want to cache?

In Zend Framework caching is operated by frontends:

* **Core** is a generic cache frontend and is extended by other classes. You will use this most of the time.
* **Output** is an output-capturing frontend. It utilizes output buffering in PHP to capture everything between its start() and end() methods.
* **FunctionFrontend** caches the results of function calls. It has a single main method named call() which takes a function name and parameters for the call in an array.
* **ClassFrontend** is different from **Function** because it allows caching of object and static method calls
* **File** is a frontend driven by the modification time of a "master file". It's really interesting for examples in configuration or templates issues. It's also possible to use multiple master files.
* **Page** is like **Output** but designed for a complete page. It's impossible to use Page for caching only a single block.
* **Capture** is **Page** but this class is specifically designed to operate in concert only with the **Static** backend to assist in caching entire pages of HTML / XML or other content to a physical static file on the local filesystem.

You can find more infos and options on [Zend Framework cache frontends](http://framework.zend.com/manual/en/zend.cache.frontends.html).

### Backends: How do you want to cache it?

Cache records are stored through backend adapters:

* **File** stores cache records into files
* **Memcached** stores cache records into a memcached server
* **Sqlite** stores cache records into a SQLite database
* **Apc** stores cache records in shared memory through the APC extension
* **Xcache** stores cache records in shared memory through the [XCache](http://xcache.lighttpd.net/) extension
* **StaticBackend** works in concert with Zend_Cache_Frontend_Capture (the two must be used together) to save the output from requests as static files. This means the static files are served directly on subsequent requests without any involvement of PHP or Zend Framework at all.
* **TwoLevels** stores cache records in two other backends: a fast one (but limited) like Apc, Memcache... and a "slow" one like File, Sqlite
* **ZendPlatform** uses content caching API of the Zend Platform product.

You can find more infos and options on [Zend Framework cache backends](http://framework.zend.com/manual/en/zend.cache.backends.html).

## KnpZendCacheBundle usage

Let's say you have a variable `$myLastTweets`.

To compute this variable, you have to call an API, decode some content, create some objects… this takes time.
You only want to compute it every 2 hours.

* We'll define a Core frontend with a 2 hours life time and serialization of the variable
* We'll use a File backend for now. In production, we could perhaps decide to use Memcache or APC : it's just a matter of changing one config file

So in order to do that, we'll have to:

* Define a new cache **template** in our config file
* Use it from the service container


#### 1 - Define a new cache template

```yaml
# app/config.yml
knp_zend_cache:
    templates:
        tweets_and_stuff:
            frontend:
                name: Core
                options:
                    lifetime: 7200
                    automatic_serialization: true
            backend:
                name: File
                options:
                    cache_dir: %kernel.root_dir%/cache/%kernel.environment%
```
#### 2 - Use it

Get the cache you declared from the service container:

```php
<?php

// Given you have access to the container $container
$cache = $container->get('knp_zend_cache.manager')->getCache('tweets_and_stuff');

// Or if you are in a controller:
$cache = $this->get('knp_zend_cache.manager')->getCache('tweets_and_stuff');

// see if a cache already exists:
if (false === ($myLastTweets = $cache->load('last_tweets'))) {

    // cache miss: call the webservice and do stuff
    $myLastTweets = $tweetService->doComplexStuff();

    $cache->save($myLastTweets, 'last_tweets');
}

return $myLastTweets;
```

That's it!
