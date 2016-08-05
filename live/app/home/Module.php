<?php
namespace ITECH\Home;

use Phalcon\Loader;

class Module
{
    
    public function registerAutoloaders() {
        $loader = new Loader();
        $loader->registerNamespaces(array(
            'ITECH\Datasource\Model' => ROOT . '/app/datasource/model/',
            'ITECH\Datasource\Repository' => ROOT . '/app/datasource/repository/',
            'ITECH\Datasource\Lib' => ROOT . '/app/datasource/lib/',
            'ITECH\Home\Controller' => ROOT . '/app/home/controller/',
            'ITECH\Home\Component' => ROOT . '/app/home/component/',
            'ITECH\Home\Form' => ROOT . '/app/home/form/',
            'ITECH\Home\Form\Validator' => ROOT . '/app/home/form/validator/',
            'ITECH\Home\Lib' => ROOT . '/app/home/lib/'
        ));
        $loader->register();
    }

    public function registerServices($di) {

    }
}