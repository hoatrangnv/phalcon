<?php
namespace ITECH\Auth;

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
            'ITECH\Auth\Controller' => ROOT . '/app/auth/controller/',
            'ITECH\Auth\Form' => ROOT . '/app/auth/form/',
            'ITECH\Auth\Form\Validator' => ROOT . '/app/auth/form/validator/',
            'ITECH\Auth\Lib' => ROOT . '/app/auth/lib/'
        ));
        $loader->register();
    }

    public function registerServices($di)
    {

    }
}