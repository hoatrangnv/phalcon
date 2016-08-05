<?php
namespace ITECH\Admin\Controller;

use Phalcon\Exception;
use ITECH\Admin\Controller\BaseController;
use ITECH\Admin\Form\OrderForm;
use ITECH\Datasource\Repository\OrderRepository;
use ITECH\Datasource\Model\Order;
use ITECH\Datasource\Model\Transport;
use ITECH\Datasource\Lib\Util;
use ITECH\Datasource\Lib\Constant;

class OrderController extends BaseController
{
    /**
     * @author Vu.Tran
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
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        
        $params = array(
            'conditions' => array(
                'q' => $q
            ),
            'page' => (int)$page,
            'limit' => (int)$limit
        );

        $cache_name = md5(serialize(array(
            'OrderController',
            'indexAction',
            'OrderRepository',
            'getListPagination',
             $params
        )));

        $result = $this->cache->get($cache_name);
        if (!$result) {
            $category_repository = new OrderRepository();
            $result = $category_repository->getListPagination($params);
            $this->cache->save($cache_name, $result);
        }

        $transports = array();
        foreach( Transport::find() as $key => $item) {
            $transports[$key]['title'] = $item->title;
        }
        $page_header = 'Danh sách đơn hàng';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');
        $search_box = true;
        
        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'search_box' => $search_box,
            'page' => $page,
            'q' => $q,
            'cache_name' => $cache_name,
            'result' => $result,
            'transports' => $transports
        ));
        $this->view->pick('order/index');
    }
}