<?php
namespace ITECH\Admin\Controller;

use Phalcon\Logger;
use Phalcon\Exception;
use ITECH\Admin\Controller\BaseController;
use ITECH\Admin\Form\LinkForm;
use ITECH\Admin\Form\LinkGroupForm;
use ITECH\Datasource\Model\LinkGroup;
use ITECH\Datasource\Model\Link;
use ITECH\Datasource\Model\Category;
use ITECH\Datasource\Model\Article;
use ITECH\Datasource\Repository\LinkGroupRepository;
use ITECH\Admin\Component\LinkComponent;
use ITECH\Datasource\Lib\Util;
use ITECH\Datasource\Lib\Constant;

class LinkController extends BaseController
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
            'conditions' => array('q' => $q),
            'page' => (int)$page,
            'limit' => (int)$limit,
            'order' => 'ITECH\Datasource\Model\LinkGroup.ordering ASC'
        );

        $cache_name = md5(serialize(array(
            'LinkController',
            'indexAction',
            'GroupLinkRepository',
            'getListPagination',
             $params
        )));

        $result = $this->cache->get($cache_name);
        if (!$result) {
            $link_group_repository = new LinkGroupRepository();
            $result = $link_group_repository->getListPagination($params);
            $this->cache->save($cache_name, $result);
        }
        
        $params = array();
        $group_cache_name = md5(serialize(array(
            'LinkController',
            'indexAction',
            'groupLink',
            $params
        )));
        
        $group_link_layout = $this->cache->get($group_cache_name);
        if (!$group_link_layout) {
            
            $group_link_layout = array();
            $link_component = new LinkComponent();
            
            foreach ($result->items as $group) {
                $links = Link::find(array(
                    'conditions' => 'group_id = :group_id: and parent_id = :parent_id:',
                    'bind' => array(
                            'group_id' => $group->id,
                            'parent_id' => 0
                        ),
                    'order' => 'ordering ASC'
                ));
                
                if ($links) {
                    $link_layout = '<ol class="dd-list">';
                    foreach ($links as $link) {
                        $query = array(
                            'id' => $link->id,
                            'page' => $page,
                            'q' => $q,
                            'cache_name' => $cache_name,
                            'group_cache_name' => $group_cache_name
                        );
                        
                        $link_layout .= '<li class="dd-item dd3-item" data-id="' . $link->id . '">
                                            <div class="dd-handle dd3-handle"></div>
                                            <div class="dd3-content">
                                                ' . $link->name . '
                                                <div class="visible-md visible-lg hidden-sm hidden-xs float-right">
                                                    <a class="btn btn-squared btn-xs btn-default tooltips" data-original-title="Sửa" data-placement="top" href="' 
                                                    . $this->url->get(array('for' => 'link_edit', 'query' =>'?' . http_build_query($query))) . '">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a class="btn btn-squared btn-xs btn-default tooltips" data-original-title="Xóa" data-placement="top" href="'
                                                    . $this->url->get(array('for' => 'link_delete', 'query' =>'?' . http_build_query($query))) .'">
                                                        <i class="fa fa-times fa fa-white"></i>
                                                    </a>
                                                </div>
                                            </div>';
                        $params = array(
                            'conditions' => array(
                                'group_id' => $group->id,
                                'parent_id' => $link->id
                            )
                        );
                        
                        $sub_link_layout = '';
                        $link_layout .= $link_component->sub($params, $sub_link_layout, $page, $q, $cache_name, $group_cache_name);
                        $link_layout .= '</li>';
                    }

                    $link_layout .= '</ol>';
                } else {
                    $link_layout = '';
                }
                
                $group_link_layout[$group->id] = $link_layout;  

            }
            $this->cache->save($group_cache_name, $group_link_layout);
        }

        $page_header = 'Danh sách nhóm liên kết';
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
            'group_cache_name' => $group_cache_name,
            'result' => $result,
            'group_link_layout' => $group_link_layout
        ));
        $this->view->pick('link/index');   
    }

    /**
     * @author Vu.Tran
     */
    public function editAction()
    {
        $id = $this->request->getQuery('id', array('int'), -1);  
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $cache_name = $this->request->getQuery('cache_name', array('trim'), '');
        $group_cache_name = $this->request->getQuery('group_cache_name', array('trim'), '');

        $link = Link::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ));

        if (!$link) {
            throw new Exception('Liên kết này không tồn tại.');
        }

        $form = new LinkForm($link);
        if ($this->request->isPost()) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }
            $form->bind($this->request->getPost(), $link);
            if (!$form->isValid()) {
                $this->flashSession->error('Vui lòng nhập liên kết.');
            } else {
                if($this->request->getPost('url') == '' && $this->request->getPost('category_product') == '' && $this->request->getPost('category_article') == '' && $this->request->getPost('article_select') == '' && $this->request->getPost('product_select') == '' && $this->request->getPost('page') == '') {
                    $this->flashSession->error('Vui lòng chọn 1 liên kết.');
                } else {
                    $link->name = Util::upperFirstLetter($this->request->getPost('name'));
                    $link->title = Util::niceWordsByChars($this->request->getPost('name'));
                    $link->rel = Util::niceWordsByChars($this->request->getPost('name'));
                    if ($this->request->getPost('url') != '') {
                        $link->url = $this->request->getPost('url');
                    }
                    
                    if ($this->request->getPost('url') == '' && $this->request->getPost('category_product') != '' ) {
                        $category_product = Category::findFirst(array(
                            'conditions' => 'id = :id:',
                            'bind' => array('id' => $this->request->getPost('category_product'))
                        ));
                        if (!$category_product->slug) {
                            $category_product->slug = Util::slug($category_product->name);
                        }
                        $link->url = $this->config->application->base_home_url . $category_product->slug . '.html';
                    }
                    
                    if ($this->request->getPost('url') == '' && $this->request->getPost('category_product') == '' && $this->request->getPost('category_article') != '' ) {
                        $category_article = Category::findFirst(array(
                            'conditions' => 'id = :id:',
                            'bind' => array('id' => $this->request->getPost('category_article'))
                        ));
                        if (!$category_article->slug) {
                            $category_article->slug = Util::slug($category_article->name);
                        }
                        $link->url = $this->config->application->base_home_url . $category_article->slug . '.html';
                    }                                  
                    
                    if ($this->request->getPost('url') == '' && $this->request->getPost('category_product') == '' && $this->request->getPost('category_article') == '' && $this->request->getPost('article_select') != '' ) {
                        $article = Article::findFirst(array(
                            'conditions' => 'id = :id: and module = :module:',
                            'bind' => array(
                                'id' => $this->request->getPost('article_select'),
                                'module' => Constant::MODULE_ARTICLES
                            )
                        ));
                        if (!$article->alias) {
                            $article->alias = Util::slug($article->title);
                        }
                        $link->url = $this->config->application->base_home_url . $article->alias . '/' . $article->id;
                    }
                    
                    if ($this->request->getPost('url') == '' && $this->request->getPost('category_product') == '' && $this->request->getPost('category_article') == '' && $this->request->getPost('article_select') == '' && $this->request->getPost('product_select') != '') {
                        $article = Article::findFirst(array(
                            'conditions' => 'id = :id: and module = :module:',
                            'bind' => array(
                                'id' => $this->request->getPost('product_select'),
                                'module' => Constant::MODULE_PRODUCTS
                            )
                        ));
                        if (!$article->alias) {
                            $article->alias = Util::slug($article->title);
                        }
                        $link->url = $this->config->application->base_home_url . $article->slug . '/' . $article->id;
                    }
                    
                    if ($this->request->getPost('url') == '' && $this->request->getPost('category_product') == '' && $this->request->getPost('category_article') == '' && $this->request->getPost('article_select') == '' && $this->request->getPost('product_select') == '' && $this->request->getPost('page') != '' ) {
                        $article = Article::findFirst(array(
                            'conditions' => 'id = :id: and module = :module:',
                            'bind' => array(
                                'id' => $this->request->getPost('page'),
                                'module' => Constant::MODULE_PAGES
                            )
                        ));
                        if (!$article->alias) {
                            $article->alias = Util::slug($article->title);
                        }
                        $link->url = $this->config->application->base_home_url . $article->alias . '/' . $article->id;
                    }
                    
                    try {
                        if (!$link->update()) {
                            $message = $link->getMessages();
                            if (isset($message[0])) {
                                $this->flashSession->error($message[0]->getMessage());
                            } else {
                                $this->flashSession->error('Lỗi, không thể cập nhật.');
                            }
                        } else {
                            if ($cache_name != '') {
                                $this->cache->delete($cache_name);
                            }
                            
                            if ($group_cache_name != '') {
                                $this->cache->delete($group_cache_name);
                            }
                            $this->flashSession->success('Cập nhật thành công.');
                            $query = array(
                                'id' => $id,
                                'page' => $page,
                                'q' => $q,
                                'cache_name' => $cache_name
                            );
                            return $this->response->redirect(array('for' => 'link_edit', 'query' => '?' . http_build_query($query)));
                        }
                    } catch (Exception $e) {
                        $this->logger->log('[LinkController][editAction] ' . $e->getMessage(), Logger::ERROR);
                        throw new Exception($e->getMessage());
                    }
                }
            }
        }

        $page_header = 'Chỉnh sửa liên kết';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => 'Danh sách liên kết', 'url' => $this->url->get(array('for' => 'link')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'cache_name' => $cache_name,
            'link' => $link,
            'form' => $form,
            'page' => $page,
            'q' => $q
        ));
        $this->view->pick('link/edit');
    }
    
    /**
     * @author Vu.Tran
     */
    public function addAction()
    {
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $cache_name = $this->request->getQuery('cache_name', array('trim'), '');
        $group_cache_name = $this->request->getQuery('group_cache_name', array('trim'), '');

        $link = new Link();
        $form = new LinkForm();
        if ($this->request->isPost()) {
            /*if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }*/
            $form->bind($this->request->getPost(), $link); 
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin không hợp lệ.');
            } else {
                if($this->request->getPost('url') == '' && $this->request->getPost('category_product') == '' && $this->request->getPost('category_article') == '' && $this->request->getPost('article_select') == '' && $this->request->getPost('product_select') == '' && $this->request->getPost('page') == '') {
                    $this->flashSession->error('Vui lòng chọn 1 liên kết.');
                } else {
                    $link->name = Util::upperFirstLetter($this->request->getPost('name'));
                    $link->title = Util::niceWordsByChars($this->request->getPost('name'));
                    $link->rel = Util::niceWordsByChars($this->request->getPost('name'));
                    $link->parent_id = 0;
                    $link->ordering = 0;
                    if ($this->request->getPost('url') != '') {
                        $link->url = $this->request->getPost('url');
                    }
                    
                    if ($this->request->getPost('url') == '' && $this->request->getPost('category_product') != '' ) {
                        $category_product = Category::findFirst(array(
                            'conditions' => 'id = :id:',
                            'bind' => array('id' => $this->request->getPost('category_product'))
                        ));
                        if (!$category_product->slug) {
                            $category_product->slug = Util::slug($category_product->name);
                        }
                        $link->url = $this->config->application->base_home_url . $category_product->slug . '.html';
                    }
                    
                    if ($this->request->getPost('url') == '' && $this->request->getPost('category_product') == '' && $this->request->getPost('category_article') != '' ) {
                        $category_article = Category::findFirst(array(
                            'conditions' => 'id = :id:',
                            'bind' => array('id' => $this->request->getPost('category_article'))
                        ));
                        if (!$category_article->slug) {
                            $category_article->slug = Util::slug($category_article->name);
                        }
                        $link->url = $this->config->application->base_home_url . $category_article->slug . '.html';
                    }                                  
                    
                    if ($this->request->getPost('url') == '' && $this->request->getPost('category_product') == '' && $this->request->getPost('category_article') == '' && $this->request->getPost('article_select') != '' ) {
                        $article = Article::findFirst(array(
                            'conditions' => 'id = :id: and module = :module:',
                            'bind' => array(
                                'id' => $this->request->getPost('article_select'),
                                'module' => Constant::MODULE_ARTICLES
                            )
                        ));
                        if (!$article->alias) {
                            $article->alias = Util::slug($article->title);
                        }
                        $link->url = $this->config->application->base_home_url . $article->alias . '/' . $article->id;
                    }
                    
                    if ($this->request->getPost('url') == '' && $this->request->getPost('category_product') == '' && $this->request->getPost('category_article') == '' && $this->request->getPost('article_select') == '' && $this->request->getPost('product_select') != '') {
                        $article = Article::findFirst(array(
                            'conditions' => 'id = :id: and module = :module:',
                            'bind' => array(
                                'id' => $this->request->getPost('product_select'),
                                'module' => Constant::MODULE_PRODUCTS
                            )
                        ));
                        if (!$article->alias) {
                            $article->alias = Util::slug($article->title);
                        }
                        $link->url = $this->config->application->base_home_url . $article->slug . '/' . $article->id;
                    }
                    
                    if ($this->request->getPost('url') == '' && $this->request->getPost('category_product') == '' && $this->request->getPost('category_article') == '' && $this->request->getPost('article_select') == '' && $this->request->getPost('product_select') == '' && $this->request->getPost('page') != '' ) {
                        $article = Article::findFirst(array(
                            'conditions' => 'id = :id: and module = :module:',
                            'bind' => array(
                                'id' => $this->request->getPost('page'),
                                'module' => Constant::MODULE_PAGES
                            )
                        ));
                        if (!$article->alias) {
                            $article->alias = Util::slug($article->title);
                        }
                        $link->url = $this->config->application->base_home_url . $article->alias . '/' . $article->id;
                    }
                
                    try {
                        if (!$link->create()) {
                            $message = $link->getMessages();
                            if (isset($message[0])) {
                                $this->flashSession->error($message[0]->getMessage());
                            } else {
                                $this->flashSession->error('Lỗi, không thể thêm.');
                            }
                        } else {
                            if ($cache_name != '') {
                                $this->cache->delete($cache_name);
                            }

                            $params = array();
                            $group_cache_name = md5(serialize(array(
                                'LinkController',
                                'indexAction',
                                'groupLink',
                                $params
                            )));

                            if ($group_cache_name != '') {
                                $this->cache->delete($group_cache_name);
                            }
                            $this->flashSession->success('Thêm thành công.');
                        }

                        return $this->response->redirect(array('for' => 'link'));
                    } catch (Exception $e) {
                        $this->logger->log('[LinkController][addAction] ' . $e->getMessage(), Logger::ERROR);
                        throw new Exception($e->getMessage());
                    }
                }
            }
        }
        
        $page_header = 'Thêm liên kết';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => 'Danh sách liên kết', 'url' => $this->url->get(array('for' => 'link')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'link' => $link,
            'page' => $page,
            'q' => $q,
            'form' => $form
        ));
        $this->view->pick('link/add');
    }

    /**
     * @author Vu.Tran
     */
    public function deleteAction()
    {
        $id = $this->request->getQuery('id', array('int'), -1);
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');

        $link = Link::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ));

        if (!$link) {
            throw new Exception('Liên kết này không tồn tại.');
        }
        
        $query = 'DELETE FROM ITECH\Datasource\Model\Link
                WHERE ITECH\Datasource\Model\Link.id = :id:';

        try {
            
            $this->deleteSubAction($link->id);
            
            $b = $this->modelsManager->createQuery($query);
            $return = $b->execute(array(
                'id' => $link->id
            ));

            if (!$return) {
                $this->flashSession->error('Lỗi, không thể xoá.');
            } else {
                if ($cache_name != '') {
                    $this->cache->delete($cache_name);
                }
                
                $params = array();
                $group_cache_name = md5(serialize(array(
                    'LinkController',
                    'indexAction',
                    'groupLink',
                    $params
                )));

                if ($group_cache_name != '') {
                    $this->cache->delete($group_cache_name);
                }

                $this->flashSession->success('Xóa thành công.');
            }

            $query = array(
                'page' => $page,
                'q' => $q
            );

            return $this->response->redirect(array('for' => 'link', 'query' => '?' . http_build_query($query)));
        } catch (Exception $e) {
            $this->logger->log('[LinkController][deleteAction] ' . $e->getMessage(), Logger::ERROR);
            throw new Exception($e->getMessage());
        }
    }
    
    /**
     * @author Vu.Tran
     */
    private function deleteSubAction($parent)
    {
        $link = Link::find(array(
            'conditions' => 'parent_id = :parent_id:',
            'bind' => array('parent_id' => $parent)
        ));
        
        foreach ($link as $item) {
            $query = 'DELETE FROM ITECH\Datasource\Model\Link
                    WHERE ITECH\Datasource\Model\Link.id = :id:';
            $b = $this->modelsManager->createQuery($query);
            $return = $b->execute(array(
                'id' => $item->id
            ));

            if (!$return) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * @author Vu.Tran
     */
    public function addGroupAction()
    {
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $group_cache_name = $this->request->getQuery('group_cache_name', array('trim'), '');
        
        $link_group = new LinkGroup();
        $form = new LinkGroupForm();
        if ($this->request->isPost()) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }
            $form->bind($this->request->getPost(), $link_group); 
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin không hợp lệ.');
            } else {
                $link_group->name = Util::upperFirstLetter($this->request->getPost('name'));
                $link_group->slug = Util::slug($this->request->getPost('name'));
                $link_group->ordering = Util::numberOnly($this->request->getPost('ordering'));
                try {
                    if (!$link_group->create()) {
                        $message = $link_group->getMessages();
                        if (isset($message[0])) {
                            $this->flashSession->error($message[0]->getMessage());
                        } else {
                            $this->flashSession->error('Lỗi, không thể thêm.');
                        }
                    } else {
                        if ($group_cache_name != '') {
                            $this->cache->delete($group_cache_name);
                        }
                        
                        $this->flashSession->success('Thêm nhóm thành công.');
                        return $this->response->redirect(array('for' => 'link'));
                    }
                } catch (Exception $e) {
                    $this->logger->log('[LinkController][addGroupAction] ' . $e->getMessage(), Logger::ERROR);
                    throw new Exception($e->getMessage());
                }
            }
        }
        

        $page_header = 'Thêm nhóm liên kết';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => 'Danh sách liên kết', 'url' => $this->url->get(array('for' => 'link')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'page' => $page,
            'q' => $q,
            'form' => $form
        ));
        $this->view->pick('link/add_group');
    }
    
    /**
     * @author Vu.Tran
     */
    public function editGroupAction()
    {
        $id = $this->request->getQuery('id', array('int'), -1);
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $cache_name = $this->request->getQuery('cache_name', array('trim'), '');
        $group_cache_name = $this->request->getQuery('group_cache_name', array('trim'), '');
        
        
        $link_group = LinkGroup::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ));
        
        $form = new LinkGroupForm($link_group);
        if ($this->request->isPost($link_group)) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }
            $form->bind($this->request->getPost(), $link_group); 
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin không hợp lệ.');
            } else {
                $link_group->name = Util::upperFirstLetter($this->request->getPost('name'));
                $link_group->slug = Util::slug($this->request->getPost('name'));
                $link_group->ordering = Util::numberOnly($this->request->getPost('ordering'));
                try {
                    if (!$link_group->update()) {
                        $message = $link_group->getMessages();
                        if (isset($message[0])) {
                            $this->flashSession->error($message[0]->getMessage());
                        } else {
                            $this->flashSession->error('Lỗi, không thể thêm.');
                        }
                    } else {
                        if ($cache_name != '') {
                            $this->cache->delete($cache_name);
                        }
                        
                        if ($group_cache_name != '') {
                            $this->cache->delete($group_cache_name);
                        }
                        
                        $this->flashSession->success('Sửa nhóm thành công.');
                        return $this->response->redirect(array('for' => 'link'));
                    }
                } catch (Exception $e) {
                    $this->logger->log('[LinkController][editGroupAction] ' . $e->getMessage(), Logger::ERROR);
                    throw new Exception($e->getMessage());
                }
            }
        }
        

        $page_header = 'Thêm nhóm liên kết';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => 'Danh sách liên kết', 'url' => $this->url->get(array('for' => 'link')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'page' => $page,
            'q' => $q,
            'form' => $form
        ));
        $this->view->pick('link/edit_group');
    }
    
    /**
     * @author Vu.Tran
     */
    public function deleteGroupAction()
    {
        $id = $this->request->getQuery('id', array('int'), -1);
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $cache_name = $this->request->getQuery('cache_name', array('trim'), '');
        $group_cache_name = $this->request->getQuery('group_cache_name', array('trim'), '');

        $link_group = LinkGroup::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ));

        if (!$link_group) {
            throw new Exception('Nhóm liên kết này không tồn tại.');
        }
        
        try {
            $query = 'DELETE FROM ITECH\Datasource\Model\LinkGroup
                WHERE ITECH\Datasource\Model\LinkGroup.id = :id:';
            $this->deleteLinkInGroupAction($link_group->id);
            
            $b = $this->modelsManager->createQuery($query);
            $return = $b->execute(array(
                'id' => $link_group->id
            ));

            if (!$return) {
                $this->flashSession->error('Lỗi, không thể xoá.');
            } else {
                if ($cache_name != '') {
                    $this->cache->delete($cache_name);
                }

                if ($group_cache_name != '') {
                    $this->cache->delete($group_cache_name);
                }

                $this->flashSession->success('Xóa nhóm thành công.');
            }

            $query = array(
                'page' => $page,
                'q' => $q
            );

            return $this->response->redirect(array('for' => 'link', 'query' => '?' . http_build_query($query)));
        } catch (Exception $e) {
            $this->logger->log('[LinkController][deleteAction] ' . $e->getMessage(), Logger::ERROR);
            throw new Exception($e->getMessage());
        }
    }
    
    /**
     * @author Vu.Tran
     */
    private function deleteLinkInGroupAction($group_id)
    {
        $link = Link::find(array(
            'conditions' => 'group_id = :group_id:',
            'bind' => array('group_id' => $group_id)
        ));
        
        foreach ($link as $item) {
            $query = 'DELETE FROM ITECH\Datasource\Model\Link
                    WHERE ITECH\Datasource\Model\Link.id = :id:';
            $b = $this->modelsManager->createQuery($query);
            $return = $b->execute(array(
                'id' => $item->id
            ));

            if (!$return) {
                return false;
            }
        }
        
        return true;
    }
}