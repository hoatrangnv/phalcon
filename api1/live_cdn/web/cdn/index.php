<?php
use Phalcon\Mvc\Application;
use Phalcon\Mvc\Url;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Config\Adapter\Ini;
use Phalcon\DI\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Session\Adapter\Files as Session;
use Phalcon\Flash\Session as FlashSession;
use Phalcon\Crypt;
use Phalcon\Security;
use Phalcon\Exception;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Acl\Adapter\Memory as Acl;
use Phalcon\Cache\Frontend\Data;
use Phalcon\Cache\Backend\Apc;
use Phalcon\Tag;
use Phalcon\Logger\Adapter\File as Logger;

date_default_timezone_set('Asia/Bangkok');
ini_set('display_errors', true);
error_reporting(E_ALL);

define('ROOT', realpath(dirname(dirname(dirname(__FILE__)))));
define('PRODUCTION', false);
define('DEBUG', false);
define('BE_ARTICLES_IMAGE_DIR', ROOT . '/web/cdn/asset/home/img/articles/');
define('BE_PRODUCTS_IMAGE_DIR', ROOT . '/web/cdn/asset/home/img/products/'); 

try {
    $loader = new Loader();
    $loader->registerDirs(array(
        ROOT . '/app/cdn/',
        ROOT . '/app/datasource/'
    ))->register();

    $di = new FactoryDefault();

    $config = new Ini(ROOT . '/app/cdn/config/parameter.ini');
    $di->set('config', $config);

    $di->set('url', function() use ($config) {
        $url = new Url();
        $url->setBaseUri($config->application->base_url);
        return $url;
    });

    $di->set('router', function() {
        $router = new Router();
        $routers = new Ini(ROOT . '/app/cdn/config/router.ini');
        if ($routers) {
            foreach ($routers as $name => $rule) {
                $pattern = $rule->pattern;
                unset($rule->pattern);
                $router->add($pattern, $rule->toArray())->setName($name);
            }
        }
        $router->setUriSource(Router::URI_SOURCE_SERVER_REQUEST_URI);
        $router->notFound(array(
            'module' => 'cdn',
            'controller' => 'error',
            'action' => 'error404'
        ));

        $router->removeExtraSlashes(true);
        return $router;
    });

    $di->set('view', function() {
        $view = new View();
        $view->setViewsDir(ROOT . '/app/cdn/view/');
        return $view;
    });

    $di->set('tag', new Tag());

    $di->set('cache', function() use ($config) {
        $data_cache = new Data(array(
            'lifetime' => $config->cache->lifetime
        ));
        $cache = new Apc($data_cache, array(
            'prefix' => $config->cache->prefix
        ));
        return $cache;
    });

    $di->set('crypt', function() use ($config) {
        $crypt = new Crypt();
        $crypt->setKey($config->application->cookie_key);
        return $crypt;
    });

    $di->set('security', function() {
        $security = new Security();
        return $security;
    });

    $di->set('session', function() use ($config) {
        $session = new Session(array(
            'uniqueId' => $config->application->session_unique_id
        ));
        $session->start();
        return $session;
    });

    $di->set('flashSession', function() {
        return new FlashSession(array(
            'error' => 'alert alert-danger',
            'success' => 'alert alert-success',
            'warning' => 'alert alert-warning',
        ));
    });

    $di->set('dispatcher', function() {
        $dispatcher = new Dispatcher();
        $dispatcher->setDefaultNamespace('ITECH\Cdn\Controller\\');

        $events_manager = new EventsManager();
        $events_manager->attach('dispatch', function($event, $dispatcher, $exception) {
            $type = $event->getType();

            if ($type == 'beforeException') {
                if (PRODUCTION) {
                    if ($exception->getCode() == Dispatcher::EXCEPTION_HANDLER_NOT_FOUND || $exception->getCode() == Dispatcher::EXCEPTION_ACTION_NOT_FOUND) {
                        $dispatcher->forward(array(
                            'module' => 'cdn',
                            'controller' => 'error',
                            'action' => 'error404'
                        ));
                        return false;
                    } else {
                        $dispatcher->forward(array(
                            'module' => 'cdn',
                            'controller' => 'error',
                            'action' => 'error',
                            'params' => array($exception)
                        ));
                        return false;
                    }
                }
            }
        });

        $dispatcher->setEventsManager($events_manager);
        return $dispatcher;
    });

    $di->set('acl', function() {
        $acl = new Acl();
        return $acl;
    });

    $di->set('logger', function() {
        $logger = new Logger(ROOT . '/app/cdn/log/debug.log');
        return $logger;
    });

    $application = new Application($di);
    $application->registerModules(array(
        'cdn' => array(
            'className' => 'ITECH\Cdn\Module',
            'path' => ROOT . '/app/cdn/Module.php'
        )
    ));

    echo $application->handle()->getContent();
} catch (Exception $e) {
    throw new Exception($e->getMessage());
} catch (PDOException $e) {
    throw new PDOException($e->getMessage());
}