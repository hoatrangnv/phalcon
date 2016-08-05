<?php
namespace ITECH\Admin;

use Phalcon\Loader;

class Module
{
    public function registerAutoloaders()
    {
        $loader = new Loader();
        $loader->registerNamespaces(array(
            'ITECH\Datasource\Lib' => ROOT . '/app/datasource/lib/',
            'ITECH\Datasource\Model' => ROOT . '/app/datasource/model/',
            'ITECH\Datasource\Repository' => ROOT . '/app/datasource/repository/',
            'ITECH\Admin\Controller' => ROOT . '/app/admin/controller/',
            'ITECH\Admin\Component' => ROOT . '/app/admin/component/',
            'ITECH\Admin\Form' => ROOT . '/app/admin/form/',
            'ITECH\Admin\Form\Validator' => ROOT . '/app/admin/form/validator/',
            'ITECH\Admin\Lib' => ROOT . '/app/admin/lib/',
            'ITECH\Datasource\Upload' => ROOT . '/app/datasource/upload/', 
        ));
        $loader->register();
    }

    public function registerServices($di)
    {

    }
}