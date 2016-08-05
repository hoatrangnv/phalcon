<?php
define('START_TIME', microtime(true));

use Phalcon\Mvc\Application;
use Phalcon\Mvc\Url;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Model\MetaData\Apc as MetaDataApc;
use Phalcon\Config\Adapter\Ini;
use Phalcon\DI\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Session\Adapter\Files as Session;
use Phalcon\Flash\Session as FlashSession;
use Phalcon\Http\Response\Cookies;
use Phalcon\Crypt;
use Phalcon\Security;
use Phalcon\Exception;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Acl\Adapter\Memory as Acl;
use Phalcon\Cache\Frontend\Data;
use Phalcon\Cache\Backend\Apc;
use Phalcon\Tag;
use Phalcon\Logger\Adapter\File as Logger;
use Phalcon\Logger as LoggerConstant;

date_default_timezone_set('Asia/Bangkok');
ini_set('display_errors', true);
error_reporting(E_ALL);

define('ROOT', realpath(dirname(dirname(dirname(__FILE__)))));

try {
    $loader = new Loader();
    $loader->registerDirs(array(
        ROOT . '/app/home/',
        ROOT . '/app/datasource/'
    ))->register();

    $di = new FactoryDefault();

    $config = new Ini(ROOT . '/app/home/config/parameter.ini');
    $di->setShared('config', $config);

    $di->setShared('db', function() use ($config) {
        $debug = false;
        $connection = new Mysql(array(
            'host' => $config->db->host,
            'port' => $config->db->port,
            'username' => $config->db->username,
            'password' => $config->db->password,
            'dbname' => $config->db->dbname,
            'charset' => $config->db->charset
        ));

        if ($debug) {
            $e = new EventsManager();
            $logger = new Logger(ROOT . '/app/home/log/db_master_' . date('Ymd') . '.log');

            $e->attach('db', function($event, $connection) use ($logger) {
                if ($event->getType() == 'beforeQuery') {
                    $sql = $connection->getSQLVariables();

                    if (count($sql)) {
                      $logger->log($connection->getSQLStatement() . ' ' . join(', ', $sql), LoggerConstant::INFO);
                    } else {
                      $logger->log($connection->getSQLStatement(), LoggerConstant::INFO);
                    }
                }
            });
            $connection->setEventsManager($e);
        }

        return $connection;
    });

    $di->setShared('db_slave', function() use ($config) {
        $debug = false;
        $connection = new Mysql(array(
            'host' => $config->db_slave->host,
            'port' => $config->db->port,
            'username' => $config->db_slave->username,
            'password' => $config->db_slave->password,
            'dbname' => $config->db_slave->dbname,
            'charset' => $config->db_slave->charset
        ));

        if ($debug) {
            $e = new EventsManager();
            $logger = new Logger(ROOT . '/app/home/log/db_slave_' . date('Ymd') . '.log');

            $e->attach('db', function($event, $connection) use ($logger) {
                if ($event->getType() == 'beforeQuery') {
                    $sql = $connection->getSQLVariables();

                    if (count($sql)) {
                      $logger->log($connection->getSQLStatement() . ' ' . join(', ', $sql), LoggerConstant::INFO);
                    } else {
                      $logger->log($connection->getSQLStatement(), LoggerConstant::INFO);
                    }
                }
            });
            $connection->setEventsManager($e);
        }

        return $connection;
    });

    $di->setShared('url', function() use ($config) {
        $url = new Url();
        $url->setBaseUri($config->application->base_url);
        return $url;
    });

    $di->setShared('router', function() {
        $router = new Router(false);
        $routers = new Ini(ROOT . '/app/home/config/router.ini');

        if ($routers) {
            foreach ($routers as $name => $rule) {
                $pattern = $rule->pattern;
                unset($rule->pattern);
                $router->add($pattern, $rule->toArray())->setName($name);
            }
        }

        $router->setUriSource(Router::URI_SOURCE_SERVER_REQUEST_URI);
        $router->notFound(array(
            'module' => 'home',
            'controller' => 'error',
            'action' => 'error404'
        ));

        $router->removeExtraSlashes(true);
        return $router;
    });

    $di->setShared('view', function() {
        $view = new View();
        $view->setViewsDir(ROOT . '/app/home/view/');
        return $view;
    });

    $di->setShared('tag', new Tag());
    
    $di->setShared('cache', function() use ($config) {
        $data_cache = new Data(array(
            'lifetime' => $config->cache->lifetime + rand(1, 100)
        ));
        $cache = new Phalcon\Cache\Backend\Xcache($data_cache, array(
            'prefix' => $config->cache->prefix
        ));

        return $cache;
    });

    $di->setShared('modelsCache', function() use ($config) {
        $data_cache = new Data(array(
            'lifetime' => $config->cache->lifetime
        ));
        $cache = new Apc($data_cache, array(
            'prefix' => $config->cache->prefix
        ));
        return $cache;
    });

    $di->setShared('modelsMetadata', function() use ($config) {
        $metaData = new MetaDataApc(array(
            'prefix' => $config->cache->metadata_prefix,
            'lifetime' => $config->cache->metadata_lifetime + rand(1, 100)
        ));
        return $metaData;
    });

    $di->setShared('security', function() {
        $security = new Security();
        return $security;
    });

    $di->setShared('session', function() {
        $session = new Session();
        $session->start();
        return $session;
    });

    $di->setShared('crypt', function() use ($config) {
        $crypt = new Crypt();
        $crypt->setKey($config->application->cookie_key);
        return $crypt;
    });

    $di->setShared('cookies', function() {
        $cookies = new Cookies();
        $cookies->useEncryption(true);
        return $cookies;
    });

    $di->setShared('flashSession', function() {
        return new FlashSession(array(
            'error' => 'alert alert-danger',
            'success' => 'alert alert-success',
            'warning' => 'alert alert-warning'
        ));
    });

    $di->setShared('dispatcher', function() {
        $dispatcher = new Dispatcher();
        $dispatcher->setDefaultNamespace('ITECH\Home\Controller\\');

        $events_manager = new EventsManager();
        $events_manager->attach('dispatch', function($event, $dispatcher, $exception) {
            $debug = false;
            $type = $event->getType();

            if ($type == 'beforeException') {
                if (!$debug) {
                    if ($exception->getCode() == Dispatcher::EXCEPTION_HANDLER_NOT_FOUND || $exception->getCode() == Dispatcher::EXCEPTION_ACTION_NOT_FOUND) {
                        $dispatcher->forward(array(
                            'module' => 'home',
                            'controller' => 'error',
                            'action' => 'error404'
                        ));
                        return false;
                    } else {
                        $dispatcher->forward(array(
                            'module' => 'home',
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

    $di->setShared('acl', function() {
        $acl = new Acl();
        return $acl;
    });

    $di->setShared('logger', function() {
        $logger = new Logger(ROOT . '/app/home/log/debug_' . date('Ymd') . '.log');
        return $logger;
    });

    $application = new Application($di);
    $application->registerModules(array(
        'home' => array(
            'className' => 'ITECH\Home\Module',
            'path' => ROOT . '/app/home/Module.php'
        ),
        'datasource' => array(
            'className' => 'ITECH\Datasource\Module',
            'path' => ROOT . '/app/datasource/Module.php'
        )
    ));

    echo $application->handle()->getContent();
} catch (Exception $e) {
    throw new Exception($e->getMessage());
} catch (PDOException $e) {
    throw new PDOException($e->getMessage());
}