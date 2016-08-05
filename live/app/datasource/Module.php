<?php
namespace ITECH\Datasource;

use Phalcon\Loader;

class Module
{
    public function registerAutoloaders()
    {
        $loader = new Loader();
        $loader->registerNamespaces(array(
            'ITECH\Datasource\Lib' => ROOT . '/app/datasource/lib/',
            'ITECH\Datasource\Upload' => ROOT . '/app/datasource/upload/',  
            'ITECH\Datasource\Model' => ROOT . '/app/datasource/model/',
            'ITECH\Datasource\Repository' => ROOT . '/app/datasource/repository/'
        ));
        $loader->register();
    }

    public function registerServices($di)
    {

    }
}