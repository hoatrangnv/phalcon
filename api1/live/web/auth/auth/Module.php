<?php
namespace TVN\Auth;

use Phalcon\Loader;

class Module
{
    public function registerAutoloaders()
    {
        $loader = new Loader();
        $loader->registerNamespaces(array(
            'TVN\Datasource\Model' => ROOT . '/app/datasource/model/',
            'TVN\Datasource\Repository' => ROOT . '/app/datasource/repository/',
            'TVN\Datasource\Lib' => ROOT . '/app/datasource/lib/',
            'TVN\Auth\Controller' => ROOT . '/app/auth/controller/',
            'TVN\Auth\Form' => ROOT . '/app/auth/form/',
            'TVN\Auth\Form\Validator' => ROOT . '/app/auth/form/validator/',
            'TVN\Auth\Lib' => ROOT . '/app/auth/lib/'
        ));
        $loader->register();
    }

    public function registerServices($di)
    {

    }
}