<?php
// Usage: php cli.php <task name> <action name> <params>
// Example: php cli.php cron cleanup
define('START_TIME', microtime(true));

use Phalcon\DI\FactoryDefault\CLI as CliDI;
use Phalcon\CLI\Console;
use Phalcon\Loader;
use Phalcon\Config\Adapter\Ini;
use Phalcon\Exception;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Logger\Adapter\File as Logger;
use Phalcon\Logger as LoggerConstant;

date_default_timezone_set('Asia/Bangkok');
ini_set('display_errors', true);
error_reporting(E_ALL);

define('ROOT', realpath(dirname(dirname(dirname(__FILE__)))));
define('DEBUG', false);

try {
    $loader = new Loader();
    $loader->registerDirs(array(
        ROOT . '/app/admin/task/'
    ));
    $loader->register();

    $loader->registerNamespaces(array(
        'ITECH\Datasource\Lib' => ROOT . '/app/datasource/lib/',
        'ITECH\Datasource\Model' => ROOT . '/app/datasource/model/'
    ))->register();

    $config = new Ini(ROOT . '/app/admin/config/parameter.ini');

    $di = new CliDI();
    $di->set('config', $config);

    $di->set('db', function() use ($config) {
        $connection = new Mysql(array(
            'host' => $config->db->host,
            'port' => $config->db->port,
            'username' => $config->db->username,
            'password' => $config->db->password,
            'dbname' => $config->db->dbname,
            'charset' => $config->db->charset
        ));

        if (DEBUG) {
            $e = new EventsManager();
            $logger = new Logger(ROOT . '/app/admin/log/cronjob_db_master_' . date('Ymd') . '.log');

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

    $di->set('db_slave', function() use ($config) {
        $connection = new Mysql(array(
            'host' => $config->db_slave->host,
            'port' => $config->db->port,
            'username' => $config->db_slave->username,
            'password' => $config->db_slave->password,
            'dbname' => $config->db_slave->dbname,
            'charset' => $config->db_slave->charset
        ));

        if (DEBUG) {
            $e = new EventsManager();
            $logger = new Logger(ROOT . '/app/admin/log/cronjob_db_slave_' . date('Ymd') . '.log');

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

    $di->set('logger', function() {
        $logger = new Logger(ROOT . '/app/admin/log/cronjob_task_' . date('Ymd') . '.log');
        return $logger;
    });

    $console = new Console();
    $console->setDI($di);

    $arguments = array();
    $params = array();
    foreach ($argv as $k => $arg) {
        if ($k == 1) {
            $arguments['task'] = $arg;
        } elseif ($k == 2) {
            $arguments['action'] = $arg;
        } elseif ($k >= 3) {
           $params[] = $arg;
        }
    }

    $arguments['params'] = $params;
    $console->handle($arguments);
} catch (Exception $e) {
    echo $e->getMessage();
} catch (PDOException $e) {
    echo $e->getMessage();
}