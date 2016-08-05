<?php
namespace ITECH\Admin\Controller;

use Phalcon\Logger;
use Phalcon\Exception;
use ITECH\Admin\Controller\BaseController;
use ITECH\Admin\Form\GalleryForm;
use ITECH\Admin\Form\CategoryForm;
use ITECH\Admin\Form\FileForm;
use ITECH\Datasource\Model\Article;
use ITECH\Datasource\Model\ArticleAttachment;
use ITECH\Datasource\Model\Category;
use ITECH\Datasource\Model\File;
use ITECH\Admin\Component\CategoryComponent;
use ITECH\Datasource\Repository\FileRepository;
use ITECH\Datasource\Repository\CategoryRepository;
use ITECH\Datasource\Lib\Util;
use ITECH\Datasource\Lib\Constant;

class FileController extends BaseController
{
    /**
     * @author Vu.Tran
     */
    public function initialize()
    {
        parent::initialize();
        parent::authenticate();
        //parent::allowRole(array(Constant::ADMIN_TYPE_ROOT, Constant::ADMIN_TYPE_ADMIN, Constant::ADMIN_TYPE_ADMIN));
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
        
        $page_header = 'Danh sách file';
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
        $this->view->pick('file/index');
    }
    
    /**
     * @author Vu.Tran
     */
    public function categoryAction()
    {
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        
        $params = array(
            'conditions' => array(
                'q' => $q,
                'module' => Constant::MODULE_FILES
            ),
            'page' => (int)$page,
            'limit' => (int)$limit
        );

        $cache_name = md5(serialize(array(
            'FileController',
            'categoryAction',
            'CategoryRepository',
            'getListPagination',
             $params
        )));

        $result = $this->cache->get($cache_name);
        if (!$result) {
            $category_repository = new CategoryRepository();
            $result = $category_repository->getListPagination($params);
            $this->cache->save($cache_name, $result);
        }
        
        if ($this->request->isPost()) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }
            
            if ($this->request->getPost('ordering')) {
                $ordering = $this->request->getPost('ordering');
                if ($ordering && count($ordering) > 0) {
                    try {
                        
                        foreach ($ordering as $key => $value) {
                            $category = Category::findFirst(array(
                                'conditions' => 'id = :id:',
                                'bind' => array('id' => $key)
                            ));
                            $this->db->begin();
                            if ($category && count($category) > 0) {
                                if ($category->ordering != $value) {
                                    $category->ordering = (int)$value;
                                    
                                    if (!$category->update()) {
                                        $message = $category->getMessages();
                                        if (isset($message[0])) {
                                            $this->flashSession->error($message[0]->getMessage());
                                        } else {
                                            $this->flashSession->error('Lỗi, không thể cập nhật.');
                                            $this->db->rollback();
                                        } 
                                    } else {
                                        $this->db->commit();
                                    }
                                }
                            }  
                        }

                        $this->cache->delete($cache_name);
                        
                        $query = array(
                        'page' => $page,
                        'q' => $q
                        );
                        
                        $this->flashSession->success('Cập nhật thành công.');
                        return $this->response->redirect(array('for' => 'file_category', 'query' => '?' . http_build_query($query)));
                    } catch (Exception $e) {
                        $this->logger->log('[LinkController][editAction] ' . $e->getMessage(), Logger::ERROR);
                        throw new Exception($e->getMessage());
                    }
                }
            }
        }
        
        $page_header = 'Danh sách thư mục';
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
        $this->view->pick('file/category');
    }
    
    /**
     * @author Vu.Tran
     */
    public function addCategoryAction()
    {
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $cache_name = $this->request->getQuery('cache_name', array('trim'), '');
        
        $category = new Category();
        $category->module = Constant::MODULE_FILES;
        $category->parent_id = 0;
        
        $params = array(
            'conditions' => array(
                'module' => Constant::MODULE_FILES,
                'parent_id' => (int)0
            ),
        );
        $cache_categories = md5(serialize(array(
            'FileController',
            'addCategoryAction',
            'CategoryRepository',
            'getList',
             $params
        )));

        $categories = $this->cache->get($cache_categories);
        if (!$categories) {
            $category_repository = new CategoryRepository();
            $categories = $category_repository->getList($params);
            $this->cache->save($cache_categories, $categories);
        }
        
        $category_component = new CategoryComponent();
        
        $no_category_layout_cache_name = md5(serialize(array(
            'FileController',
            'addCategoryAction',
            Constant::MODULE_FILES,
        )));

        $no_category_layout = $this->cache->get($no_category_layout_cache_name);
        if (!$no_category_layout) {
            $no_category_layout = '<select id="form-field-select-1" class="form-control" name="parent_id">';
            $no_category_layout .= '<option value="0">Không</option>';
            $level = '';
            foreach ($categories as $item) {

                $active = '';
                if ($category->parent_id == $item->id) {
                    $active = 'selected="selected"';
                }
                $no_category_layout .= '<option ' . $active . 'value="' . $item->id . '">' . $item->name . '</option>';
                $params = array(
                    'conditions' => array(
                        'module' => Constant::MODULE_FILES,
                        'parent_id' => $item->id,
                    ),
                );
                $sub_category_layout = '';
                $no_category_layout .= $category_component->sub_select($params, $sub_category_layout, $level, $category->parent_id);

            }
            $no_category_layout .= '</select>';
            $this->cache->save($no_category_layout_cache_name, $no_category_layout);
        }
        
        $form = new CategoryForm();
        if ($this->request->isPost()) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }
            $form->bind($this->request->getPost(), $category); 
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin không hợp lệ.');
            } else {
                $ordering = Category::count();
                $category->name = Util::upperFirstLetter($this->request->getPost('name'));
                $category->slug = Util::slug($this->request->getPost('name'));
                $category->parent_id = Util::numberOnly($this->request->getPost('parent_id'));
                $category->hits = Constant::HITS;
                $category->article_count = Constant::COUNT;
                $category->ordering = $ordering + 1;
                $category->status = Constant::CATEGORY_STATUS_ACTIVED;
                $category->created_at = date('Y-m-d H:i:s');
                $category->module = Constant::MODULE_FILES;
                
                try {
                    if (!$category->create()) {
                        $message = $category->getMessages();
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
                    
                    return $this->response->redirect(array('for' => 'file_category'));
                } catch (Exception $e) {
                    throw new Exception($e->getMessage());
                }
            }
        }

        $page_header = 'Thêm thư mục';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => 'Danh sách danh mục', 'url' => $this->url->get(array('for' => 'category')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'page' => $page,
            'q' => $q,
            'module' => Constant::MODULE_FILES,
            'category_layout' => $no_category_layout,
            'form' => $form,
            'category' => $category
        ));
        $this->view->pick('file/add_category');
    }
    
    /**
     * @author Vu.Tran
     */
    public function editCategoryAction()
    {
        $id = $this->request->getQuery('id', array('int'), -1);  
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $cache_name = $this->request->getQuery('cache_name', array('trim'), '');

        $category = Category::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ));

        if (!$category) {
            throw new Exception('Danh mục này không tồn tại.');
        }
        
        $params = array(
            'conditions' => array(
                'module' => Constant::MODULE_FILES,
                'parent_id' => (int)0
            ),
        );
        $cache_categories = md5(serialize(array(
            'FileController',
            'editCategoryAction',
            'CategoryRepository',
            'getList',
             $params
        )));

        $categories = $this->cache->get($cache_categories);
        if (!$categories) {
            $category_repository = new CategoryRepository();
            $categories = $category_repository->getList($params);
            $this->cache->save($cache_categories, $categories);
        }
        
        $category_component = new CategoryComponent();
        
        $cache_category_layout = md5(serialize(array(
            'CategoryController',
            'editAction',
            Constant::MODULE_FILES,
            $category->id
        )));

        $category_layout = $this->cache->get($cache_category_layout);
        if (!$category_layout) {
            $category_layout = '<select id="form-field-select-1" class="form-control" name="parent_id">';
            $category_layout .= '<option value="0">Không</option>';
            $level = '';
            foreach ($categories as $item) {

                $active = '';
                if ($category->parent_id == $item->id) {
                    $active = 'selected="selected"';
                }
                $category_layout .= '<option ' . $active . 'value="' . $item->id . '">' . $item->name . '</option>';
                $params = array(
                    'conditions' => array(
                        'module' => Constant::MODULE_FILES,
                        'parent_id' => $item->id,
                    ),
                );
                $sub_category_layout = '';
                $category_layout .= $category_component->sub_select($params, $sub_category_layout, $level, $category->parent_id);

            }
            $category_layout .= '</select>';
            $this->cache->save($cache_category_layout, $category_layout);
        }
        
        $form = new CategoryForm($category);
        if ($this->request->isPost()) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }
            $form->bind($this->request->getPost(), $category);
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin không hợp lệ.');
            } else {
                $category->name = Util::upperFirstLetter($this->request->getPost('name'));
                $category->slug = Util::slug($this->request->getPost('name'));
                $category->parent_id = Util::numberOnly($this->request->getPost('parent_id'));
                
                try {
                    if (!$category->update()) {
                        $message = $category->getMessages();
                        if (isset($message[0])) {
                            $this->flashSession->error($message[0]->getMessage());
                        } else {
                            $this->flashSession->error('Lỗi, không thể cập nhật.');
                        }
                    } else {
                        if ($cache_name != '') {
                            $this->cache->delete($cache_name);
                        }
                        
                        if ($cache_categories != '') {
                            $this->cache->delete($cache_categories);
                        }
                        
                        if ($cache_category_layout != '') {
                            $this->cache->delete($cache_category_layout);
                        }
                        
                        $this->flashSession->success('Cập nhật thành công.');
                    }

                    $query = array(
                        'id' => $id,
                        'page' => $page,
                        'q' => $q,
                        'cache_name' => $cache_name
                    );
                    return $this->response->redirect(array('for' => 'file_category_edit', 'query' => '?' . http_build_query($query)));
                } catch (Exception $e) {
                    throw new Exception($e->getMessage());
                }
            }
        }
        
        $page_header = 'Sửa thư mục';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => 'Danh sách thư mục', 'url' => $this->url->get(array('for' => 'file_category')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');
        
        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'form' => $form,
            'category' => $category,
            'category_layout' => $category_layout,
            'page' => $page,
            'q' => $q,
            'cache_name' => $cache_name
        ));
        $this->view->pick('file/edit_category');
    }
    
    /**
     * @author Vu.Tran
     */
    public function addAction()
    {
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $cache_name = $this->request->getQuery('cache_name', array('trim'), '');
        
        $user = $this->session->get('USER');
        
        $category = new Category();    
        $params = array(
            'conditions' => array(
                'module' => Constant::MODULE_FILES,
                'parent_id' => (int)0
            ),
        );
        $cache_categories = md5(serialize(array(
            'FileController',
            'addAction',
            'CategoryRepository',
            'getList',
             $params
        )));

        $categories = $this->cache->get($cache_categories);
        if (!$categories) {
            $category_repository = new CategoryRepository();
            $categories = $category_repository->getList($params);
            $this->cache->save($cache_categories, $categories);
        }
        
        $category_component = new CategoryComponent();
        
        $no_category_layout_cache_name = md5(serialize(array(
            'FileController',
            'addAction',
            Constant::MODULE_FILES,
        )));

        $no_category_layout = $this->cache->get($no_category_layout_cache_name);
        if (!$no_category_layout) {
            $no_category_layout = '<select id="form-field-select-1" class="form-control" name="category">';
            $no_category_layout .= '<option value="0">Không</option>';
            $level = '';
            foreach ($categories as $item) {
                $active = '';
                if ($category->parent_id == $item->id) {
                    $active = 'selected="selected"';
                }
                $no_category_layout .= '<option ' . $active . 'value="' . $item->id . '">' . $item->name . '</option>';
                $params = array(
                    'conditions' => array(
                        'module' => Constant::MODULE_FILES,
                        'parent_id' => $item->id,
                    ),
                );
                $sub_category_layout = '';
                $no_category_layout .= $category_component->sub_select($params, $sub_category_layout, $level, $category->parent_id);

            }
            $no_category_layout .= '</select>';
            $this->cache->save($no_category_layout_cache_name, $no_category_layout);
        }
        
        $files = new File();
        $form = new FileForm(); 
        
        if ($this->request->isPost()) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }
            $form->bind($this->request->getPost(), $files);
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin không hợp lệ.');
            } else {
                $files->category_id = $this->request->getPost('category', array('int'), Constant::CATEGORY_DEFAULT);
                $files->title = Util::upperFirstLetter($this->request->getPost('title'));
                $files->created_at = time();
                $files->updated_at = time();
                $files->created_by = $user['id'];
                $files->updated_by = $user['id'];
                $files->created_ip = $this->request->getClientAddress();
                if ($this->request->hasFiles()) {
                    $file = $this->request->getUploadedFiles();

                    if (isset($file[0])) {
                        $resource = array(
                            'name' => $file[0]->getName(),
                            'type' => $file[0]->getType(),
                            'tmp_name' => $file[0]->getTempName(),
                            'error' => $file[0]->getError(),
                            'size' => $file[0]->getSize()
                        );
                        $files->file_type = $resource['type'];
                        $files->file_size = $resource['size'];

                        $response = parent::uploadLocalImage(ROOT . '/web/admin/asset/images/', '', 500, $resource);

                        if (!empty($response['status']) && $response['status'] == Constant::CODE_SUCCESS) {
                            $files->file_name = $response['result'];
                            parent::uploadRemoteImage(ROOT . '/web/admin/asset/images/', 'default', $files->file_name);
                            parent::deleteLocalImage(ROOT . '/web/admin/asset/images/', $files->file_name);
                        }
                    }
                }

                $this->db->begin();
                try {
                    if (!$files->create()) {
                        $message = $files->getMessages();
                        if (isset($message[0])) {
                            $this->flashSession->error($message[0]->getMessage());
                        } else {
                            $this->flashSession->error('Lỗi, không thể thêm mới.');
                        }
                    } else { 
                        $this->flashSession->success('Thêm mới thành công.');
                        $this->db->commit();
                        
                        $query = array(
                            'id' => $files->id,
                            'category_id' => $files->category_id,
                            'page' => $page,
                            'q' => $q,
                            'cache_name' => $cache_name
                        );
                        return $this->response->redirect(array('for' => 'file_edit', 'query' => '?' . http_build_query($query)));
                    }  
                } catch (Exception $e) {
                    $this->db->rollback();
                    $this->logger->log('[FileController][addAction] ' . $e->getMessage(), Logger::ERROR);
                    throw new Exception($e->getMessage());
                }
            }
        }
        
        $page_header = 'Thêm file';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'page' => $page,
            'q' => $q,
            'file' => $files,
            'form' => $form,
            'cache_name' => $cache_name,
            'category_layout' => $no_category_layout
        ));
        $this->view->pick('file/add');
    }
    
    /**
     * @author Vu.Tran
     */
    public function editAction() {
        $id = $this->request->getQuery('id', array('int'), -1);
        $category_id = $this->request->getQuery('category_id', array('int'), -1);
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $cache_name = $this->request->getQuery('cache_name', array('trim'), '');
        
        $user = $this->session->get('USER');
        
        $files = File::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id),
        ));
        if (!$files) {
            throw new Exception(' Không tồn tại file này');
        }
        
        $category = Category::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $category_id)
        ));
        if (!$category) {
            $category = Constant::CATEGORY_DEFAULT;
        }
                                
        $params = array(
            'conditions' => array(
                'module' => Constant::MODULE_FILES,
                'parent_id' => (int)0
            ),
        );
        $cache_categories = md5(serialize(array(
            'FileController',
            'editAction',
            'CategoryRepository',
            'getList',
             $params
        )));

        $categories = $this->cache->get($cache_categories);
        if (!$categories) {
            $category_repository = new CategoryRepository();
            $categories = $category_repository->getList($params);
            $this->cache->save($cache_categories, $categories);
        }
        
        $category_component = new CategoryComponent();
        
        $no_category_layout_cache_name = md5(serialize(array(
            'FileController',
            'editAction',
            Constant::MODULE_FILES,
        ))); 

        $no_category_layout = $this->cache->get($no_category_layout_cache_name);
        if (!$no_category_layout) {
            $no_category_layout = '<select id="form-field-select-1" class="form-control" name="category">';
            $no_category_layout .= '<option value="0">Không</option>';
            $level = '';
            foreach ($categories as $item) {

                $active = '';
                if ($category_id == $item->id) {
                    $active = 'selected="selected"';
                }
                $no_category_layout .= '<option ' . $active . 'value="' . $item->id . '">' . $item->name . '</option>';
                $params = array(
                    'conditions' => array(
                        'module' => Constant::MODULE_FILES,
                        'parent_id' => $item->id,
                    ),
                );
                $sub_category_layout = '';
                $no_category_layout .= $category_component->file_sub_select($params, $sub_category_layout, $level, $category_id);

            }
            $no_category_layout .= '</select>';
            $this->cache->save($no_category_layout_cache_name, $no_category_layout);
        }

        $files->category = $category_id;    
        $form = new FileForm($files);
        
        if ($this->request->isPost()) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }
            $form->bind($this->request->getPost(), $files);
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin không hợp lệ.');
            } else {
                    $files->category_id = $this->request->getPost('category', array('int'), Constant::CATEGORY_DEFAULT);
                    $files->title = Util::upperFirstLetter($this->request->getPost('title'));
                    $files->updated_at = time();
                    $files->updated_by = $user['id'];
                    $files->ordering = Util::numberOnly($this->request->getPost('ordering'));
                    $files->created_ip = $this->request->getClientAddress();
                    if ($this->request->hasFiles()) {
                        $file = $this->request->getUploadedFiles();
                        if (isset($file[0])) {
                            parent::deleteRemoteImage('default', $files->file_name);
                            $resource = array(
                                'name' => $file[0]->getName(),
                                'type' => $file[0]->getType(),
                                'tmp_name' => $file[0]->getTempName(),
                                'error' => $file[0]->getError(),
                                'size' => $file[0]->getSize()
                            );
                            $files->file_type = $resource['type'];
                            $files->file_size = $resource['size'];

                            $response = parent::uploadLocalImage(ROOT . '/web/admin/asset/images/', '', 500, $resource);
                            
                            if (!empty($response['status']) && $response['status'] == Constant::CODE_SUCCESS) {
                                $files->file_name = $response['result'];
                                parent::uploadRemoteImage(ROOT . '/web/admin/asset/images/', 'default', $files->file_name);
                                parent::deleteLocalImage(ROOT . '/web/admin/asset/images/', $files->file_name);
                            }
                        }
                    }
                    
                    $this->db->begin();
                    try {
                        if (!$files->update()) {
                            $message = $files->getMessages();
                            if (isset($message[0])) {
                                $this->flashSession->error($message[0]->getMessage());
                            } else {
                                $this->flashSession->error('Lỗi, không thể cập nhật.');
                            }
                        } else {
                            $this->db->commit();
                            if ($cache_name != '') {
                                $this->cache->delete($cache_name);
                            }
                            $this->flashSession->success('Cập nhật thành công.'); 
                        }
                        
                        $query = array(
                            'id' => $files->id,
                            'category_id' => $files->category_id, 
                            'page' => $page,
                            'q' => $q,
                            'cache_name' => $cache_name,
                        );
                        return $this->response->redirect(array('for' => 'file_edit', 'query' => '?' . http_build_query($query)));  
                    } catch (Exception $e) {
                        $this->db->rollback();
                        $this->logger->log('[FileController][editAction] ' . $e->getMessage(), Logger::ERROR);
                        throw new Exception($e->getMessage());
                    }
            }
        }
        
        $page_header = 'Sửa file';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'page' => $page,
            'q' => $q,
            'category_id' => $category_id,
            'form' => $form,
            'file' => $files,
            'cache_name' => $cache_name,
            'category_layout' => $no_category_layout
        ));
        $this->view->pick('file/edit');
    }
    
    /**
     * @author Vu.Tran
     */
    public function deleteAction()
    {
        $id = $this->request->getQuery('id', array('int'), -1);
        $category_id = $this->request->getQuery('category_id', array('int'), -1);
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $cache_name = $this->request->getQuery('cache_name', array('trim'), '');

        $file = File::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ));
        if (!$file) {
            throw new Exception(' Không tồn tại file này');
        }

        $file_category = FileCategory::findFirst(array(
            'conditions' => 'file_id = :file_id: and category_id = :category_id:',
            'bind' => array(
                'file_id' => $file->id,
                'category_id' => $category_id
            )
        ));
        if (!$file_category) {
            throw new Exception(' File này không tồn tại trong danh mục');
        }
        
        try {
            $this->db->begin();
            if (!$file->delete()) {
                $message = $file->getMessages();
                if (isset($message[0])) {
                    $this->flashSession->error($message[0]->getMessage());
                } else {
                    $this->flashSession->error('Lỗi, không thể xóa file.');
                }
            } else {
                if (!$file_category->delete()) {
                    $message = $file->getMessages();
                    if (isset($message[0])) {
                        $this->flashSession->error($message[0]->getMessage());
                    } else {
                        $this->flashSession->error('Lỗi, không thể xóa file.');
                    }
                    $this->db->rollback();
                } else {
                    $this->db->commit();
                    
                    if ($cache_name != '') {
                        $this->cache->delete($cache_name);
                    }
                    
                    $this->flashSession->success('Xóa file thành công.');
                }
            }

            $query = array(
                'page' => $page,
                'q' => $q
            );
            return $this->response->redirect(array('for' => 'file', 'query' => '?' . http_build_query($query)));
        } catch (Exception $e) {
            $this->db->rollback();
            $this->logger->log('[FileController][deleteAction] ' . $e->getMessage(), Logger::ERROR);
            throw new Exception($e->getMessage());
        }
    }
    
    /**
     * @author Vu.Tran
     */
    public function galleryAction()
    {
        $id = $this->request->getQuery('id', array('int'), -1);
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $cache_name = $this->request->getQuery('cache_name', array('trim'), '');
        $from = $this->request->getQuery('from', array('trim'), '');
        
        $user = $this->session->get('USER');
        
        $params = array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id),
        );
        
        $article_cache_name = md5(serialize(array(
            'FileController',
            'galleryAction',
            $params
        )));
        $article = $this->cache->get($article_cache_name);
        if (!$article) {
            $article = Article::findFirst($params);
            if (!$article) {
                throw new Exception(' Không tồn tại bài viết này');
            }
            
            $this->cache->save($article_cache_name, $article);
        }
        
        $article_attachments = $article->getArticleAttachment();
        
        $article_attachment = new ArticleAttachment();
        $form = new GalleryForm(); 
        
        if ($this->request->isPost()) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }
            $form->bind($this->request->getPost(), $article_attachment);
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin không hợp lệ.');
            } else {
                $article_attachment->article_id = $article->id;
                $article_attachment->title = Util::upperFirstLetter($this->request->getPost('title'));
                $article_attachment->created_at = time();
                $article_attachment->updated_at = time();
                $article_attachment->created_by = $user['id'];
                $article_attachment->updated_by = $user['id'];
                $article_attachment->ordering = Util::numberOnly($this->request->getPost('ordering'));
                $article_attachment->created_ip = $this->request->getClientAddress();
                if ($this->request->hasFiles()) {
                    $file = $this->request->getUploadedFiles();

                    if (isset($file[0])) {
                        $resource = array(
                            'name' => $file[0]->getName(),
                            'type' => $file[0]->getType(),
                            'tmp_name' => $file[0]->getTempName(),
                            'error' => $file[0]->getError(),
                            'size' => $file[0]->getSize()
                        );
                        $article_attachment->file_type = $resource['type'];
                        $article_attachment->file_size = $resource['size'];                        

                        $response = parent::uploadLocalImage(ROOT . '/web/admin/asset/images/', '', 500, $resource);
                        
                        if (!empty($response['status']) && $response['status'] == Constant::CODE_SUCCESS) {
                            $article_attachment->file_name = $response['result'];
                            parent::uploadRemoteImage(ROOT . '/web/admin/asset/images/', 'default', $article_attachment->file_name);
                            parent::deleteLocalImage(ROOT . '/web/admin/asset/images/', $article_attachment->file_name);
                        }
                        
                        try {
                            $file = new File();
                            $file->title = Util::upperFirstLetter($this->request->getPost('title'));
                            $file->category_id = Constant::CATEGORY_DEFAULT;
                            $file->created_at = time();
                            $file->updated_at = time();
                            $file->created_by = $user['id'];
                            $file->updated_by = $user['id'];
                            $file->created_ip = $this->request->getClientAddress();
                            $file->file_name = $article->image;
                            $file->file_type = $resource['type'];
                            $file->file_size = $resource['size'];             
                            if (!$file->create()) {
                                $message = $file->getMessages();
                                if (isset($message[0])) {
                                    $this->flashSession->error($message[0]->getMessage());
                                } else {
                                    $this->flashSession->error('Lỗi, không thể thêm hình ảnh vào quản lý file.');
                                }
                            }
                        } catch (Exception $e) {
                            $this->logger->log('[FileController][galleryAction] ' . $e->getMessage(), Logger::ERROR);
                            throw new Exception($e->getMessage());
                        }
                    }
                }
                
                try {
                    if (!$article_attachment->create()) {
                        $message = $article_attachment->getMessages();
                        if (isset($message[0])) {
                            $this->flashSession->error($message[0]->getMessage());
                        } else {
                            $this->flashSession->error('Lỗi, không thể cập nhật.');
                        }
                    } else {                        
                        $this->flashSession->success('Thêm thành công.');
                        
                        $query = array(
                            'id' => $article_attachment->id,
                            'page' => $page,
                            'q' => $q,
                            'cache_name' => $cache_name,
                            'from' => $from
                        );
                        
                        return $this->response->redirect(array('for' => 'file_gallery_edit', 'query' => '?' . http_build_query($query)));
                    }
                } catch (Exception $e) {
                    $this->logger->log('[FileController][galleryAction] ' . $e->getMessage(), Logger::ERROR);
                    throw new Exception($e->getMessage());
                }
            }
        }
        
        $page_header = 'Thêm hình ảnh';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'page' => $page,
            'q' => $q,
            'form' => $form,
            'article' => $article,
            'article_attachments' => $article_attachments,
            'article_attachment' => $article_attachment,
            'cache_name' => $cache_name,
            'from' => $from
        ));
        $this->view->pick('file/gallery');
    }
    
    /**
     * @author Vu.Tran
     */
    public function editGalleryAction()
    {
        $id = $this->request->getQuery('id', array('int'), -1);
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $cache_name = $this->request->getQuery('cache_name', array('trim'), '');
        $from = $this->request->getQuery('from', array('trim'), '');
        
        $user = $this->session->get('USER');
        
        $article_attachment = ArticleAttachment::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id),
        ));
        if (!$article_attachment) {
            throw new Exception(' Không tồn tại hình ảnh này');
        }
        
        $params = array();
        $article_cache_name = md5(serialize(array(
            'FileController',
            'editGalleryAction',
            'getArticle',
            $article_attachment->article_id,
            $params
        )));
        $article = $this->cache->get($article_cache_name);
        if (!$article) {
            $article = $article_attachment->getArticle();
            $this->cache->save($article_cache_name, $article);
        }
        
        if (!$article) {
            throw new Exception(' Không tồn tại bài viết này');
        }
        
        $article_attachments = $article->getArticleAttachment();
        
        
        $form = new GalleryForm($article_attachment);
        
        if ($this->request->isPost()) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }
            $form->bind($this->request->getPost(), $article_attachment);
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin không hợp lệ.');
            } else {
                $article_attachment->article_id = $article->id;
                $article_attachment->title = Util::upperFirstLetter($this->request->getPost('title'));
                $article_attachment->updated_at = time();
                $article_attachment->updated_by = $user['id'];
                $article_attachment->ordering = Util::numberOnly($this->request->getPost('ordering'));
                $article_attachment->created_ip = $this->request->getClientAddress();
                if ($this->request->hasFiles()) {
                    $file = $this->request->getUploadedFiles();

                    if (isset($file[0])) {
                        $resource = array(
                            'name' => $file[0]->getName(),
                            'type' => $file[0]->getType(),
                            'tmp_name' => $file[0]->getTempName(),
                            'error' => $file[0]->getError(),
                            'size' => $file[0]->getSize()
                        );
                        $article_attachment->file_type = $resource['type'];
                        $article_attachment->file_size = $resource['size'];

                        $response = parent::uploadLocalImage(ROOT . '/web/admin/asset/images/', '', 500, $resource);
                        
                        if (!empty($response['status']) && $response['status'] == Constant::CODE_SUCCESS) {
                            $article_attachment->file_name = $response['result'];
                            parent::uploadRemoteImage(ROOT . '/web/admin/asset/images/', 'default', $article_attachment->file_name);
                            parent::deleteLocalImage(ROOT . '/web/admin/asset/images/', $article_attachment->file_name);
                        }
                        
                        try {
                            $file = new File();
                            $file->title = Util::upperFirstLetter($this->request->getPost('title'));
                            $file->category_id = Constant::CATEGORY_DEFAULT;
                            $file->created_at = time();
                            $file->updated_at = time();
                            $file->created_by = $user['id'];
                            $file->updated_by = $user['id'];
                            $file->created_ip = $this->request->getClientAddress();
                            $file->file_name = $article->image;
                            $file->file_type = $resource['type'];
                            $file->file_size = $resource['size'];             
                            if (!$file->create()) {
                                $message = $file->getMessages();
                                if (isset($message[0])) {
                                    $this->flashSession->error($message[0]->getMessage());
                                } else {
                                    $this->flashSession->error('Lỗi, không thể thêm hình ảnh vào quản lý file.');
                                }
                            }
                        } catch (Exception $e) {
                            $this->logger->log('[ArticleController][editAction] ' . $e->getMessage(), Logger::ERROR);
                            throw new Exception($e->getMessage());
                        }
                    }
                }
                
                try {
                    if (!$article_attachment->update()) {
                        $message = $article_attachment->getMessages();
                        if (isset($message[0])) {
                            $this->flashSession->error($message[0]->getMessage());
                        } else {
                            $this->flashSession->error('Lỗi, không thể cập nhật.');
                        }
                    } else {                        
                        $this->flashSession->success('Cập nhật thành công.');
                    }
                    
                    $query = array(
                        'id' => $article_attachment->id,
                        'page' => $page,
                        'q' => $q,
                        'cache_name' => $cache_name,
                        'from' => $from
                    );
                    return $this->response->redirect(array('for' => 'file_gallery_edit', 'query' => '?' . http_build_query($query)));
                } catch (Exception $e) {
                    $this->logger->log('[FileController][editGalleryAction] ' . $e->getMessage(), Logger::ERROR);
                    throw new Exception($e->getMessage());
                }
            }
        }
        
        $page_header = 'Sửa hình ảnh';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'page' => $page,
            'q' => $q,
            'form' => $form,
            'article' => $article,
            'article_attachments' => $article_attachments,
            'article_attachment' => $article_attachment,
            'cache_name' => $cache_name,
            'from' => $from
        ));
        $this->view->pick('file/edit_gallery');
    } 
    
    /**
     * @author Vu.Tran
     */
    public function deleteImageGalleryAction()
    {
        $id = $this->request->getQuery('id', array('int'), -1);
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $cache_name = $this->request->getQuery('cache_name', array('trim'), '');
        $from = $this->request->getQuery('from', array('trim'), '');

        $article_attachment = ArticleAttachment::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id),
        ));
        if (!$article_attachment) {
            throw new Exception(' Không tồn tại hình ảnh này');
        }
        
        $params = array();
        $article_cache_name = md5(serialize(array(
            'FileController',
            'deleteImageGalleryAction',
            'getArticle',
            $article_attachment->article_id,
            $params
        )));
        $article = $this->cache->get($article_cache_name);
        if (!$article) {
            $article = $article_attachment->getArticle();
            $this->cache->save($article_cache_name, $article);
        }
        
        if (!$article) {
            throw new Exception(' Không tồn tại bài viết này');
        }
        
        switch ($from) {
            case 'article':
                $folder = 'articles';
                break;
            case 'product':
                $folder = 'products';
                break;
            case 'page':
                $folder = 'pages';
                break;          
        }
        parent::deleteRemoteImage($folder, $article_attachment->file_name);
        try {
            if (!$article_attachment->delete()) {
                $message = $article_attachment->getMessages();
                if (isset($message[0])) {
                    $this->flashSession->error($message[0]->getMessage());
                } else {
                    $this->flashSession->error('Lỗi, không thể xóa hình ảnh.');
                }
            } else {
                $this->flashSession->success('Xóa hình ảnh thành công.');
            }

            $query = array(
                'id' => $article->id,
                'page' => $page,
                'q' => $q,
                'cache_name' => $cache_name,
                'from' => $from
            );
            return $this->response->redirect(array('for' => 'file_gallery', 'query' => '?' . http_build_query($query)));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    
    /**
     * @author Vu.Tran
     */
    public function uploadRemoteAction() { 
        $user = $this->session->get('USER');
        $response['status'] = Constant::CODE_SUCCESS;
        $response['message'] = 'Success.';
        if ($this->request->isAjax()) {
            if ($this->request->isPost()) {
                $file_url = $this->request->getPost('file_url', array('striptags', 'trim'), '');
                $file_name_new = $this->request->getPost('file_name_new', array('striptags', 'trim'), '');
                $file_name_old = $this->request->getPost('file_name_old', array('striptags', 'trim'), '');
                $resource = $this->request->getPost('resource', array('striptags', 'trim'), '');
                parent::uploadRemoteImage(ROOT . '/web/admin' . $file_url, 'content', $file_name_new);
                parent::deleteLocalImage(ROOT . '/web/admin' . $file_url, $file_name_new);
                
                $file = new File();
                $file_name = explode('.', $file_name_old);
                $file->title = Util::upperFirstLetter($file_name[0]);
                $file->category_id = Constant::CATEGORY_DEFAULT;
                $file->created_at = time();
                $file->updated_at = time();
                $file->created_by = $user['id'];
                $file->updated_by = $user['id'];
                $file->created_ip = $this->request->getClientAddress();
                $file->file_name = $file_name_new;
                $file->file_type = $resource['type'];
                $file->file_size = $resource['size'];             
                if (!$file->create()) {
                    $message = $file->getMessages();
                    if (isset($message[0])) {
                        $response['status'] = Constant::CODE_ERROR;
                        $response['message'] = 'Lỗi, không thể thêm file.';
                    } else {
                        $response['status'] = Constant::CODE_ERROR;
                        $response['message'] = 'Lỗi, không thể thêm file.';
                    }
                }
            }
        } 
        
        parent::outputJSON($response);
    }
}
