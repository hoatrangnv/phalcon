<?php
namespace ITECH\Api;

use Phalcon\Loader;

class Module
{
    public function registerAutoloaders()
    {
        $loader = new Loader();
        $loader->registerNamespaces(array(
            'ITECH\Datasource\Model' => ROOT . '/app/datasource/model/',
            'ITECH\Datasource\Repository' => ROOT . '/app/datasource/repository/',
            'ITECH\Datasource\Lib' => ROOT . '/app/datasource/lib/',
            'ITECH\Api\Controller' => ROOT . '/app/api/controller/',
            'ITECH\Api\Lib' => ROOT . '/app/api/lib/'
        ));
        $loader->register();
    }

    public function registerServices($di)
    {

    }
}