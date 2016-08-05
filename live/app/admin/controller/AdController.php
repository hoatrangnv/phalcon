<?php
namespace ITECH\Admin\Controller;

use Phalcon\Exception;
use ITECH\Admin\Controller\BaseController;
use ITECH\Admin\Form\BannerZoneForm;
use ITECH\Admin\Form\BannerForm;
use ITECH\Datasource\Model\BannerZone;
use ITECH\Datasource\Model\Banner;
use ITECH\Datasource\Model\BannerBind;
use ITECH\Datasource\Model\Category;
use ITECH\Datasource\Repository\BannerZoneRepository;
use ITECH\Datasource\Repository\BannerRepository;
use ITECH\Datasource\Lib\Util;
use ITECH\Datasource\Lib\Constant;

class AdController extends BaseController
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
            'AdController',
            'indexAction',
            'BannerRepository',
            'getListPagination',
             $params
        )));

        $result = $this->cache->get($cache_name);
        if (!$result) {
            $banner_repository = new BannerRepository();
            $result = $banner_repository->getListPagination($params);
            $this->cache->save($cache_name, $result);
        }
        
        $page_header = 'Danh sách quảng cáo';
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
            'result' => $result
        ));
        $this->view->pick('ad/index');
    }
    
    /**
     * @author Vu.Tran
     */
    public function addAction()
    {
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $cache_name = $this->request->getQuery('cache_name', array('trim'), '');

        $banner = new Banner();
        $form = new BannerForm();
        if ($this->request->isPost()) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }
            $form->bind($this->request->getPost(), $banner); 
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin không hợp lệ.');
            } else {
                if($this->request->getPost('url') == '' && $this->request->getPost('category_product') == '' && $this->request->getPost('category_article') == '' && $this->request->getPost('article_select') == '' && $this->request->getPost('product_select') == '' && $this->request->getPost('page') == '') {
                    $this->flashSession->error('Vui lòng chọn 1 liên kết.');
                } else {
                    $banner->name = Util::upperFirstLetter($this->request->getPost('name'));
                    $banner->banner_zone_id = Util::numberOnly($this->request->getPost('banner_zone_id'));
                    $banner->expired_at = strtotime($this->request->getPost('expired_at'));
                    $banner->status = Constant::STATUS_ACTIVED;
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
                        $banner->url = $this->config->application->base_home_url . $category_product->slug . '.html';
                    }
                    
                    if ($this->request->getPost('url') == '' && $this->request->getPost('category_product') == '' && $this->request->getPost('category_article') != '' ) {
                        $category_article = Category::findFirst(array(
                            'conditions' => 'id = :id:',
                            'bind' => array('id' => $this->request->getPost('category_article'))
                        ));
                        if (!$category_article->slug) {
                            $category_article->slug = Util::slug($category_article->name);
                        }
                        $banner->url = $this->config->application->base_home_url . $category_article->slug . '.html';
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
                        $banner->url = $this->config->application->base_home_url . $article->alias . '/' . $article->id;
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
                        $banner->url = $this->config->application->base_home_url . $article->slug . '/' . $article->id;
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
                        $banner->url = $this->config->application->base_home_url . $article->alias . '/' . $article->id;
                    }
                    
                    if ($this->request->hasFiles()) {
                        
                        $banner_zone = BannerZone::findFirst(array(
                            'conditions' => 'id = :id:',
                            'bind' => array('id' => Util::numberOnly($this->request->getPost('banner_zone_id')))
                        ));  
                        $file = $this->request->getUploadedFiles();
                        if (isset($file[0])) {
                            $resource = array(
                                'name' => $file[0]->getName(),
                                'type' => $file[0]->getType(),
                                'tmp_name' => $file[0]->getTempName(),
                                'error' => $file[0]->getError(),
                                'size' => $file[0]->getSize()
                            );

                            $response = parent::uploadLocalImage(ROOT . '/web/admin/asset/images/', '', $banner_zone->width, $resource);
                            
                            if (!empty($response['status']) && $response['status'] == Constant::CODE_SUCCESS) {
                                $banner->image = $response['result'];
                                parent::uploadRemoteImage(ROOT . '/web/admin/asset/images/', 'banner', $banner->image);
                                parent::deleteLocalImage(ROOT . '/web/admin/asset/images/', $banner->image);
                            }
                        }
                    }
                    
                    $this->db->begin();
                
                    try {
                        if (!$banner->create()) {
                            $message = $banner->getMessages();
                            if (isset($message[0])) {
                                $this->flashSession->error($message[0]->getMessage());
                            } else {
                                $this->flashSession->error('Lỗi, không thể thêm.');
                            }
                        } else {
                            if ($cache_name != '') {
                                $this->cache->delete($cache_name);
                            }
                            $this->flashSession->success('Thêm thành công.');
                            $this->db->commit();
                        }

                        $query = array(
                            'id' => $banner->id, 
                            'page' => $page,
                            'q' => $q,
                            'cache_name' => $cache_name,
                        );
                        return $this->response->redirect(array('for' => 'ad_edit', 'query' => '?' . http_build_query($query)));
                    } catch (Exception $e) {
                        $this->db->rollback();
                        $this->logger->log('[AdController][addAction] ' . $e->getMessage(), Logger::ERROR);
                        throw new Exception($e->getMessage());
                    }
                }
            }
        }
        
        $page_header = 'Thêm quảng cáo';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => 'Danh sách quảng cáo', 'url' => $this->url->get(array('for' => 'ad')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'banner' => $banner,
            'page' => $page,
            'q' => $q,
            'form' => $form
        ));
        $this->view->pick('ad/add');
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
        
        $banner = Banner::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id),
        ));
        if (!$banner) {
            throw new Exception(' Không tồn tại quảng cáo này');
        }
        
        $banner->expired_at = date('d-m-Y', $banner->expired_at);
        $form = new BannerForm($banner);
        if ($this->request->isPost()) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }
            $form->bind($this->request->getPost(), $banner); 
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin không hợp lệ.');
            } else {
                if($this->request->getPost('url') == '' && $this->request->getPost('category_product') == '' && $this->request->getPost('category_article') == '' && $this->request->getPost('article_select') == '' && $this->request->getPost('product_select') == '' && $this->request->getPost('page') == '') {
                    $this->flashSession->error('Vui lòng chọn 1 liên kết.');
                } else {
                    $banner->name = Util::upperFirstLetter($this->request->getPost('name'));
                    $banner->banner_zone_id = Util::numberOnly($this->request->getPost('banner_zone_id'));
                    $banner->expired_at = strtotime($this->request->getPost('expired_at'));
                    $banner->status = Constant::STATUS_ACTIVED;
                    if ($this->request->getPost('url') != '') {
                        $banner->url = $this->request->getPost('url');
                    }
                    
                    if ($this->request->getPost('url') == '' && $this->request->getPost('category_product') != '' ) {
                        $category_product = Category::findFirst(array(
                            'conditions' => 'id = :id:',
                            'bind' => array('id' => $this->request->getPost('category_product'))
                        ));
                        if (!$category_product->slug) {
                            $category_product->slug = Util::slug($category_product->name);
                        }
                        $banner->url = $this->config->application->base_home_url . $category_product->slug . '.html';
                    }
                    
                    if ($this->request->getPost('url') == '' && $this->request->getPost('category_product') == '' && $this->request->getPost('category_article') != '' ) {
                        $category_article = Category::findFirst(array(
                            'conditions' => 'id = :id:',
                            'bind' => array('id' => $this->request->getPost('category_article'))
                        ));
                        if (!$category_article->slug) {
                            $category_article->slug = Util::slug($category_article->name);
                        }
                        $banner->url = $this->config->application->base_home_url . $category_article->slug . '.html';
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
                        $banner->url = $this->config->application->base_home_url . $article->alias . '/' . $article->id;
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
                        $banner->url = $this->config->application->base_home_url . $article->slug . '/' . $article->id;
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
                        $banner->url = $this->config->application->base_home_url . $article->alias . '/' . $article->id;
                    }
                    
                    if ($this->request->hasFiles()) {
                        
                        $banner_zone = BannerZone::findFirst(array(
                            'conditions' => 'id = :id:',
                            'bind' => array('id' => Util::numberOnly($this->request->getPost('banner_zone_id')))
                        ));  
                        $file = $this->request->getUploadedFiles();
                        if (isset($file[0])) {
                            $resource = array(
                                'name' => $file[0]->getName(),
                                'type' => $file[0]->getType(),
                                'tmp_name' => $file[0]->getTempName(),
                                'error' => $file[0]->getError(),
                                'size' => $file[0]->getSize()
                            );

                            $response = parent::uploadLocalImage(ROOT . '/web/admin/asset/images/', '', $banner_zone->width, $resource);
                            
                            if (!empty($response['status']) && $response['status'] == Constant::CODE_SUCCESS) {
                                $banner->image = $response['result'];
                                parent::uploadRemoteImage(ROOT . '/web/admin/asset/images/', 'banner', $banner->image);
                                parent::deleteLocalImage(ROOT . '/web/admin/asset/images/', $banner->image);
                            }
                        }
                    }
                    
                    $this->db->begin();
                
                    try {
                        if (!$banner->update()) {
                            $message = $banner->getMessages();
                            if (isset($message[0])) {
                                $this->flashSession->error($message[0]->getMessage());
                            } else {
                                $this->flashSession->error('Lỗi, không thể thêm.');
                            }
                        } else {
                            if ($cache_name != '') {
                                $this->cache->delete($cache_name);
                            }
                            $this->flashSession->success('Sửa thành công.');
                            $this->db->commit();
                        }

                        $query = array(
                            'id' => $banner->id, 
                            'page' => $page,
                            'q' => $q,
                            'cache_name' => $cache_name,
                        );
                        return $this->response->redirect(array('for' => 'ad_edit', 'query' => '?' . http_build_query($query)));
                    } catch (Exception $e) {
                        $this->db->rollback();
                        $this->logger->log('[AdController][addAction] ' . $e->getMessage(), Logger::ERROR);
                        throw new Exception($e->getMessage());
                    }
                }
            }
        }
        
        $page_header = 'Sửa quảng cáo';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => 'Danh sách quảng cáo', 'url' => $this->url->get(array('for' => 'ad')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'banner' => $banner,
            'page' => $page,
            'q' => $q,
            'form' => $form
        ));
        $this->view->pick('ad/edit');
    }
    
    /**
     * @author Vu.Tran
     */
    public function deleteAction()
    {
        $page_header = 'Xóa quảng cáo';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs
        ));
        $this->view->pick('ad/index');
    }
    
    /**
     * @author Vu.Tran
     */
    public function positionAction()
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
            'FileController',
            'indexAction',
            'FileRepository',
            'getListPagination',
             $params
        )));

        $result = $this->cache->get($cache_name);
        if (!$result) {
            $banner_zone_repository = new BannerZoneRepository();
            $result = $banner_zone_repository->getListPagination($params);
            $this->cache->save($cache_name, $result);
        }
        
        $page_header = 'Danh sách vị trí quảng cáo';
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
            'result' => $result
        ));
        $this->view->pick('ad/position');
    }
    
    /**
     * @author Vu.Tran
     */
    public function positionAddAction()
    {
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $cache_name = $this->request->getQuery('cache_name', array('trim'), '');

        $banner_zone = new BannerZone();
        $form = new BannerZoneForm();
        if ($this->request->isPost()) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            } 
            
            $form->bind($this->request->getPost(), $banner_zone); 
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin không hợp lệ.');
            } else {
                try {
                    $banner_zone->name = Util::upperFirstLetter($this->request->getPost('name'));
                    $banner_zone->slug = Util::slug($this->request->getPost('name'));
                    $banner_zone->width = Util::upperFirstLetter($this->request->getPost('width'));
                    $banner_zone->height = Util::upperFirstLetter($this->request->getPost('height'));
                    
                    if (!$banner_zone->create()) {
                        $message = $banner_zone->getMessages();
                        if (isset($message[0])) {
                            $this->flashSession->error($message[0]->getMessage());
                        } else {
                            $this->flashSession->error('Lỗi, không thể thêm.');
                        }
                    } else {
                        if ($cache_name != '') {
                            $this->cache->delete($cache_name);
                        }
                        $this->flashSession->success('Thêm thành công.');
                    }
                    
                    $query = array(
                        'page' => $page,
                        'q' => $q
                    );
                    
                    return $this->response->redirect(array('for' => 'ad_position', 'query' => '?' . http_build_query($query)));
                } catch (Exception $e) {
                    $this->logger->log('[AdController][positionAddAction] ' . $e->getMessage(), Logger::ERROR);
                    throw new Exception($e->getMessage());
                }
            }
        }
            
        $page_header = 'Thêm vị trí quảng cáo';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'page' => $page,
            'q' => $q,
            'form' => $form
        ));
        $this->view->pick('ad/add_position');
    }
    
    /**
     * @author Vu.Tran
     */
    public function positionEditAction()
    {
        $id = $this->request->getQuery('id', array('int'), -1);
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $cache_name = $this->request->getQuery('cache_name', array('trim'), '');

        
        $banner_zone = BannerZone::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ));
        
        if (!$banner_zone) {
            throw new Exception('Vị trí quảng cáo này không tồn tại.');
        }
        $form = new BannerZoneForm($banner_zone);
        if ($this->request->isPost()) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            } 
            
            $form->bind($this->request->getPost(), $banner_zone); 
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin không hợp lệ.');
            } else {
                try {
                    $banner_zone->name = Util::upperFirstLetter($this->request->getPost('name'));
                    $banner_zone->slug = Util::slug($this->request->getPost('name'));
                    $banner_zone->width = Util::upperFirstLetter($this->request->getPost('width'));
                    $banner_zone->height = Util::upperFirstLetter($this->request->getPost('height'));
                    
                    if (!$banner_zone->update()) {
                        $message = $banner_zone->getMessages();
                        if (isset($message[0])) {
                            $this->flashSession->error($message[0]->getMessage());
                        } else {
                            $this->flashSession->error('Lỗi, không thể thêm.');
                        }
                    } else {
                        if ($cache_name != '') {
                            $this->cache->delete($cache_name);
                        }
                        $this->flashSession->success('Thêm thành công.');
                    }
                    
                    $query = array(
                        'page' => $page,
                        'q' => $q
                    );
                    
                    return $this->response->redirect(array('for' => 'ad_position', 'query' => '?' . http_build_query($query)));
                } catch (Exception $e) {
                    $this->logger->log('[AdController][positionAddAction] ' . $e->getMessage(), Logger::ERROR);
                    throw new Exception($e->getMessage());
                }
            }
        }
        
        $page_header = 'Sửa vị trí quảng cáo';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'page' => $page,
            'q' => $q,
            'form' => $form
        ));
        $this->view->pick('ad/edit_position');
    }
}