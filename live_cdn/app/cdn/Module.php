<?php
namespace ITECH\Cdn;

use Phalcon\Loader;

class Module
{
    public function registerAutoloaders()
    {
        $loader = new Loader();
        $loader->registerNamespaces(array(
            'ITECH\Cdn\Controller' => ROOT . '/app/cdn/controller/',
            'ITECH\Cdn\Lib' => ROOT . '/app/cdn/lib/'
        ));
        $loader->register();
    }

    public function registerServices($di)
    {

    }
}