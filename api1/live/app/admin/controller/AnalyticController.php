<?php
namespace ITECH\Admin\Controller;

use Phalcon\Exception;
use ITECH\Admin\Controller\BaseController;
use ITECH\Datasource\Model\Article;
use ITECH\Datasource\Model\Order;
use ITECH\Datasource\Model\User;
use ITECH\Datasource\Model\File;
use ITECH\Datasource\Lib\Constant;

class AnalyticController extends BaseController
{
    /**
     * @author Cuong.Bui
     */
    public function initialize()
    {
        parent::initialize();
        parent::authenticate();
        parent::allowRole(array(Constant::ADMIN_TYPE_ROOT, Constant::ADMIN_TYPE_ADMIN));
    }

    /**
     * @author Vu.Tran
     */
    public function indexAction()
    {
        $params = array(
            'conditions' => 'module = :module:',
            'bind' => array(
                'module' => Constant::MODULE_PRODUCTS
            )
        );
        $cache_name = md5(serialize(array(
            'AnalyticController',
            'indexAction',
            'Article',
            'count',
            $params
        )));

        $count_product = $this->cache->get($cache_name);
        if (!$count_product) {
            $count_product = Article::count($params);
            $this->cache->save($cache_name, $count_product);
        }
        
        $params = array(
            'conditions' => 'module = :module:',
            'bind' => array(
                'module' => Constant::MODULE_ARTICLES
            )
        );
        $cache_name = md5(serialize(array(
            'AnalyticController',
            'indexAction',
            'Article',
            'count',
            $params
        )));

        $count_article = $this->cache->get($cache_name);
        if (!$count_article) {
            $count_article = Article::count($params);
            $this->cache->save($cache_name, $count_article);
        }
        
        $cache_name = md5(serialize(array(
            'AnalyticController',
            'indexAction',
            'Order',
            'count'
        )));

        $count_order = $this->cache->get($cache_name);
        if (!$count_order) {
            $count_order = Order::count();
            $this->cache->save($cache_name, $count_order);
        }
        
        $cache_name = md5(serialize(array(
            'AnalyticController',
            'indexAction',
            'User',
            'count'
        )));

        $count_user = $this->cache->get($cache_name);
        if (!$count_user) {
            $count_user = User::count();
            $this->cache->save($cache_name, $count_user);
        }
        
        $cache_name = md5(serialize(array(
            'AnalyticController',
            'indexAction',
            'File',
            'count'
        )));

        $count_file = $this->cache->get($cache_name);
        if (!$count_file) {
            $count_file = File::count();
            $this->cache->save($cache_name, $count_file);
        }
        
        $page_header = 'Thống kê';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'count_product' => $count_product,
            'count_article' => $count_article,
            'count_order' => $count_order,
            'count_user' => $count_user,
            'count_file' => $count_file
        ));
        
        $this->view->pick('analytic/index');
    }
    
    /**
     * @author Vu.Tran
     */
    public function articleAction()
    {
        $params = array(
            'conditions' => 'module = :module:',
            'bind' => array(
                'module' => Constant::MODULE_PRODUCTS
            )
        );
        $cache_name = md5(serialize(array(
            'AnalyticController',
            'indexAction',
            'Article',
            'count',
            $params
        )));

        $count_product = $this->cache->get($cache_name);
        if (!$count_product) {
            $count_product = Article::count($params);
            $this->cache->save($cache_name, $count_product);
        }
        
        $params = array(
            'conditions' => 'module = :module:',
            'bind' => array(
                'module' => Constant::MODULE_ARTICLES
            )
        );
        $cache_name = md5(serialize(array(
            'AnalyticController',
            'indexAction',
            'Article',
            'count',
            $params
        )));

        $count_article = $this->cache->get($cache_name);
        if (!$count_article) {
            $count_article = Article::count($params);
            $this->cache->save($cache_name, $count_article);
        }
        
        $cache_name = md5(serialize(array(
            'AnalyticController',
            'indexAction',
            'Order',
            'count'
        )));

        $count_order = $this->cache->get($cache_name);
        if (!$count_order) {
            $count_order = Order::count();
            $this->cache->save($cache_name, $count_order);
        }
        
        $cache_name = md5(serialize(array(
            'AnalyticController',
            'indexAction',
            'User',
            'count'
        )));

        $count_user = $this->cache->get($cache_name);
        if (!$count_user) {
            $count_user = User::count();
            $this->cache->save($cache_name, $count_user);
        }
        
        $cache_name = md5(serialize(array(
            'AnalyticController',
            'indexAction',
            'File',
            'count'
        )));

        $count_file = $this->cache->get($cache_name);
        if (!$count_file) {
            $count_file = File::count();
            $this->cache->save($cache_name, $count_file);
        }
        
        $page_header = 'Thống kê';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'count_product' => $count_product,
            'count_article' => $count_article,
            'count_order' => $count_order,
            'count_user' => $count_user,
            'count_file' => $count_file
        ));
        
        $this->view->pick('analytic/article');
    }
    
    /**
     * @author Vu.Tran
     */
    public function productAction()
    {
        $params = array(
            'conditions' => 'module = :module:',
            'bind' => array(
                'module' => Constant::MODULE_PRODUCTS
            )
        );
        $cache_name = md5(serialize(array(
            'AnalyticController',
            'indexAction',
            'Article',
            'count',
            $params
        )));

        $count_product = $this->cache->get($cache_name);
        if (!$count_product) {
            $count_product = Article::count($params);
            $this->cache->save($cache_name, $count_product);
        }
        
        $params = array(
            'conditions' => 'module = :module:',
            'bind' => array(
                'module' => Constant::MODULE_ARTICLES
            )
        );
        $cache_name = md5(serialize(array(
            'AnalyticController',
            'indexAction',
            'Article',
            'count',
            $params
        )));

        $count_article = $this->cache->get($cache_name);
        if (!$count_article) {
            $count_article = Article::count($params);
            $this->cache->save($cache_name, $count_article);
        }
        
        $cache_name = md5(serialize(array(
            'AnalyticController',
            'indexAction',
            'Order',
            'count'
        )));

        $count_order = $this->cache->get($cache_name);
        if (!$count_order) {
            $count_order = Order::count();
            $this->cache->save($cache_name, $count_order);
        }
        
        $cache_name = md5(serialize(array(
            'AnalyticController',
            'indexAction',
            'User',
            'count'
        )));

        $count_user = $this->cache->get($cache_name);
        if (!$count_user) {
            $count_user = User::count();
            $this->cache->save($cache_name, $count_user);
        }
        
        $cache_name = md5(serialize(array(
            'AnalyticController',
            'indexAction',
            'File',
            'count'
        )));

        $count_file = $this->cache->get($cache_name);
        if (!$count_file) {
            $count_file = File::count();
            $this->cache->save($cache_name, $count_file);
        }
        
        $page_header = 'Thống kê';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'count_product' => $count_product,
            'count_article' => $count_article,
            'count_order' => $count_order,
            'count_user' => $count_user,
            'count_file' => $count_file
        ));
        
        $this->view->pick('analytic/product');
    }
}