<?php
namespace ITECH\Admin\Controller;

use Phalcon\Mvc\View;
use ITECH\Admin\Controller\BaseController;
use ITECH\Datasource\Model\Link;
use ITECH\Datasource\Repository\ArticleRepository;
use ITECH\Datasource\Repository\FileRepository;
use ITECH\Datasource\Lib\Constant;
use ITECH\Datasource\Lib\Util;

class AjaxController extends BaseController
{
    /**
     * @author Vu.Tran
     */
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * @author Vu.Tran
     */
    public function listImageAction()
    {
        
        $url = $this->config->cdn->list_image_url;
        $channel_name = $this->config->drive->channel_name;
        if ($this->request->isAjax()) {
            if ($this->request->isPost()) {
                $folder = $this->request->getPost('folder', array('striptags', 'trim'), '');
                $image_url = $this->config->asset->home_image_url . $this->config->drive->channel_name . '/' . $folder . '/';
                $params = array();
                $cache_name = md5(serialize(array(
                    'AjaxController',
                    'listImageAction',
                    $folder,
                    $params
                )));

                $result = $this->cache->get($cache_name);
                if (!$result) {
                    $post = array(
                        'folder' => $folder,
                        'channel_name' => $channel_name
                    );
                    $r = Util::curlPost($url, $post);
                    $result = json_decode($r, true);
                    $this->cache->save($cache_name, $result);
                }  
            }
        }

        $this->view->setVars(array(
            'result' => $result,
            'image_url' => $image_url
        ));
        
        $this->view->pick('ajax/list_image');
        $this->view->setMainView('layout_image');
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }
    
    /**
     * @author Vu.Tran
     */
    public function listArticleByCategoryAction()
    {
        if ($this->request->isAjax()) {
            if ($this->request->isPost()) {
                $id = $this->request->getPost('id', array('int'), -1);
                $params = array(
                    'conditions' => array(
                        'category_id' => $id,
                        'module' => Constant::MODULE_ARTICLES
                    ),
                    'order' => 'ITECH\Datasource\Model\Article.id DESC'
                );
                $cache_name = md5(serialize(array(
                    'AjaxController',
                    'listArticleByCategoryAction',
                    $id,
                    $params
                )));
                
                $result = $this->cache->get($cache_name);
                if (!$result) {
                    $article_repository = new ArticleRepository();
                    $result = $article_repository->getList($params);
                    $this->cache->save($cache_name, $result);
                }  
            }
        }

        $this->view->setVars(array(
            'result' => $result
        ));
        $this->view->pick('ajax/list_article_by_category');
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }
    
    /**
     * @author Vu.Tran
     */
    public function listProductByCategoryAction()
    {
        if ($this->request->isAjax()) {
            if ($this->request->isPost()) {
                $id = $this->request->getPost('id', array('int'), -1);
                $params = array(
                    'conditions' => array(
                        'category_id' => $id,
                        'module' => Constant::MODULE_PRODUCTS
                    ),
                    'order' => 'ITECH\Datasource\Model\Article.id DESC'
                );
                $cache_name = md5(serialize(array(
                    'AjaxController',
                    'listProductByCategoryAction',
                    $id,
                    $params
                )));
                
                $result = $this->cache->get($cache_name);
                if (!$result) {
                    $article_repository = new ArticleRepository();
                    $result = $article_repository->getList($params);
                    $this->cache->save($cache_name, $result);
                }  
            }
        }

        $this->view->setVars(array(
            'result' => $result
        ));
        $this->view->pick('ajax/list_product_by_category');
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }
    
    /**
     * @author Vu.Tran
     */
    public function sortLinkAction()
    {
        
        $url = $this->config->cdn->list_image_url;
        $channel_name = $this->config->drive->channel_name;
        if ($this->request->isAjax()) {
            if ($this->request->isPost()) {
                $links = $this->request->getPost('links', null); 
                
                foreach ($links as $key => $item) {
                    $params = array();
                    $cache_name = md5(serialize(array(
                        'AjaxController',
                        'sortLinkAction',
                        $item['id'],
                        $params
                    )));

                    $link = $this->cache->get($cache_name);
                    if (!$link) {
                        $link = Link::findFirst(array(
                            'conditions' => 'id = :id:',
                            'bind' => array('id' => $item['id'])
                        ));
                        $this->cache->save($cache_name, $link);
                    }
                    $link->ordering = $key;
                    $link->parent_id = 0;
                    if (!$link->update()) {
                        $message = $link->getMessages();
                        if (isset($message[0])) {
                            $this->flashSession->error($message[0]->getMessage());
                        } else {
                            $this->flashSession->error('Lỗi, không thể sắp xếp.');
                        }
                        break;
                    }
                    
                    if (isset($item['children'])) {
                        $this->updateSort($item['children'], $item['id']);
                    }
                }
                
                $params = array();
                $cache_group_name = md5(serialize(array(
                    'LinkController',
                    'indexAction',
                    'groupLink',
                    $params
                )));
                if($cache_group_name != '') {
                    $this->cache->delete($cache_group_name);
                }
                
                
            }
        }
        $this->view->disable();
    }
    
    /**
     * @author Vu.Tran
     */
    private function updateSort($links, $parent)
    {
        foreach ($links as $key => $item) {
            $params = array();
            $cache_name = md5(serialize(array(
                'AjaxController',
                'sortLinkAction',
                $item['id'],
                $params
            )));
            $link = $this->cache->get($cache_name);
            if (!$link) {
                $link = Link::findFirst(array(
                    'conditions' => 'id = :id:',
                    'bind' => array('id' => $item['id'])
                ));
                $this->cache->save($cache_name, $link);
            }
            $link->ordering = $key;
            $link->parent_id = $parent;
            if (!$link->update()) {
                $message = $link->getMessages();
                if (isset($message[0])) {
                    $this->flashSession->error($message[0]->getMessage());
                } else {
                    $this->flashSession->error('Lỗi, không thể thêm.');
                }
            }
            if (isset($item['children'])) {
                $this->updateSort($item['children']);
            }
        }
        
        return true;
    }
    
    /**
     * @author Vu.Tran
     */
    public function fileAction()
    {
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = Constant::PAGINATION_100;
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        
        $params = array(
            'conditions' => array(
                'q' => $q
            ),
            'page' => (int)$page,
            'limit' => (int)$limit
        );

        $cache_name = md5(serialize(array(
            'FileController',
            'indexAction',
            'FileRepository',
            'getListPagination',
             $params
        )));

        $result = $this->cache->get($cache_name);
        if (!$result) {
            $file_repository = new FileRepository();
            $result = $file_repository->getListPagination($params);
            $this->cache->save($cache_name, $result);
        }
        $response = array();
        foreach ($result->items as $item) {
            $response[] = $item;
        }
        parent::outputJSON($response);
        
    }   
}