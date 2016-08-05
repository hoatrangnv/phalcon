<?php
namespace ITECH\Admin\Controller;

use Phalcon\Exception;
use ITECH\Admin\Controller\BaseController;
use ITECH\Admin\Form\CategoryForm;
use ITECH\Admin\Form\AttributeGroupForm;
use ITECH\Admin\Form\AttributeForm;
use ITECH\Admin\Form\TagForm;
use ITECH\Datasource\Model\Category;
use ITECH\Datasource\Model\AttributeGroup;
use ITECH\Datasource\Model\Attribute;
use ITECH\Datasource\Model\Tag;
use ITECH\Datasource\Repository\CategoryRepository;
use ITECH\Datasource\Repository\AttributeGroupRepository;
use ITECH\Datasource\Repository\AttributeRepository;
use ITECH\Datasource\Repository\TagRepository;
use ITECH\Admin\Component\CategoryComponent;
use ITECH\Datasource\Lib\Util;
use ITECH\Datasource\Lib\Constant;

class CategoryController extends BaseController
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
        $module = $this->request->getQuery('module', array('striptags', 'trim'), '');

        $params = array(
            'conditions' => array(
                'q' => $q,
                'module' => $module
            ),
            'page' => (int)$page,
            'limit' => (int)$limit
        );

        $cache_name = md5(serialize(array(
            'CategoryController',
            'indexAction',
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
                            if ($category && count($category) > 0) {
                                if ($category->ordering != $value) {
                                    $category->ordering = (int)$value;

                                    if (!$category->update()) {
                                        $message = $category->getMessages();
                                        if (isset($message[0])) {
                                            $this->flashSession->error($message[0]->getMessage());
                                        } else {
                                            $this->flashSession->error('Lỗi, không thể cập nhật.');
                                        }
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
                        return $this->response->redirect(array('for' => 'category', 'query' => '?' . http_build_query($query)));
                    } catch (Exception $e) {
                        throw new Exception($e->getMessage());
                    }
                }
            }
        }

        $page_header = 'Danh sách danh mục';
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
        $this->view->pick('category/index');
    }

    /**
     * @author Vu.Tran
     */
    public function chooseAction()
    {
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $cache_name = $this->request->getQuery('cache_name', array('trim'), '');

        if ($this->request->isPost()) {
            $module = $this->request->getPost('module', array('striptags', 'trim'), Constant::MODULE_ARTICLES);

            $query = array(
                'module' => $module,
                'page' => $page,
                'q' => $q,
                'cache_name' => $cache_name
            );
            return $this->response->redirect(array('for' => 'category_add', 'query' => '?' . http_build_query($query)));
        }

        $page_header = 'Chọn loại danh mục';
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
        ));
        $this->view->pick('category/choose');

    }

    /**
     * @author Vu.Tran
     */
    public function addAction()
    {
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $module = $this->request->getQuery('module', array('striptags', 'trim'), Constant::MODULE_ARTICLES);
        $cache_name = $this->request->getQuery('cache_name', array('trim'), '');

        $module_select = Constant::moduleTypeLabel();
        if (!isset($module_select[$module])) {
            throw new Exception('Loại danh mục bạn chọn không đúng.');
        }

        $category = new Category();
        $category->module = $module;
        $category->parent_id = 0;

        $params = array(
            'conditions' => array(
                'module' => $module,
                'parent_id' => intval(0)
            ),
        );
        $cache_categories = md5(serialize(array(
            'CategoryController',
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
            'CategoryController',
            'editAction',
            $module,
            $category->id
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
                        'module' => $module,
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
            /*if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }*/
            $form->bind($this->request->getPost(), $category);
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin không hợp lệ.');
            } else {
                $ordering = Category::count();
                $category->name = Util::upperFirstLetter($this->request->getPost('name'));
                $category->slug = Util::slug($this->request->getPost('name'));
                $category->parent_id = Util::numberOnly($this->request->getPost('parent_id'));
                $category->hits = 0;
                $category->article_count = 0;
                $category->ordering = $ordering + 1;
                $category->status = Constant::CATEGORY_STATUS_ACTIVED;
                $category->created_at = date('Y-m-d H:i:s');

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

                        $response = parent::uploadLocalImage(ROOT . '/web/admin/asset/images/', '', 122, $resource);
                        if (!empty($response['status']) && $response['status'] == Constant::CODE_SUCCESS) {
                            $category->image = $response['result'];
                            parent::uploadRemoteImage(ROOT . '/web/admin/asset/images/', 'default', $category->image);
                            parent::deleteLocalImage(ROOT . '/web/admin/asset/images/', $category->image);
                        }
                    }
                }

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

                    return $this->response->redirect(array('for' => 'category'));
                } catch (Exception $e) {
                    throw new Exception($e->getMessage());
                }
            }
        }

        $page_header = 'Thêm danh mục';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => 'Danh sách danh mục', 'url' => $this->url->get(array('for' => 'category')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'page' => $page,
            'q' => $q,
            'module' => $module,
            'category_layout' => $no_category_layout,
            'form' => $form,
            'category' => $category
        ));
        $this->view->pick('category/add');
    }

    /**
     * @author Vu.Tran
     */
    public function editAction()
    {
        $id = $this->request->getQuery('id', array('int'), -1);
        $module = $this->request->getQuery('module', array('striptags', 'trim'), Constant::MODULE_ARTICLES);
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $cache_name = $this->request->getQuery('cache_name', array('trim'), '');

        $module_select = Constant::moduleTypeLabel();
        if (!isset($module_select[$module])) {
            throw new Exception('Loại danh mục bạn chọn không đúng.');
        }

        $category = Category::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ));

        if (!$category) {
            throw new Exception('Danh mục này không tồn tại.');
        }

        $params = array(
            'conditions' => array(
                'module' => $module,
                'parent_id' => intval(0)
            ),
        );
        $cache_categories = md5(serialize(array(
            'CategoryController',
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

        $cache_category_layout = md5(serialize(array(
            'CategoryController',
            'editAction',
            $module,
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
                        'module' => $module,
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

                        $response = parent::uploadLocalImage(ROOT . '/web/admin/asset/images/', '', 500, $resource);

                        if (!empty($response['status']) && $response['status'] == Constant::CODE_SUCCESS) {
                            $category->image = $response['result'];
                            parent::uploadRemoteImage(ROOT . '/web/admin/asset/images/', 'default', $category->image);
                            parent::deleteLocalImage(ROOT . '/web/admin/asset/images/', $category->image);
                        }
                    }
                }

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
                        'module' => $module,
                        'page' => $page,
                        'q' => $q,
                        'cache_name' => $cache_name
                    );
                    return $this->response->redirect(array('for' => 'category_edit', 'query' => '?' . http_build_query($query)));
                } catch (Exception $e) {
                    throw new Exception($e->getMessage());
                }
            }
        }

        $page_header = 'Sửa danh mục';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => 'Danh sách danh mục', 'url' => $this->url->get(array('for' => 'category')));
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
        $this->view->pick('category/edit');
    }

    /**
     * @author Vu.Tran
     */
    public function deleteImageAction()
    {
        $id = $this->request->getQuery('id', array('int'), -1);
        $module = $this->request->getQuery('module', array('striptags', 'trim'), 'news');
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

        parent::deleteRemoteImage('default', $category->image);
        $category->image = null;

        try {
            if (!$category->update()) {
                $message = $category->getMessages();
                if (isset($message[0])) {
                    $this->flashSession->error($message[0]->getMessage());
                } else {
                    $this->flashSession->error('Lỗi, không thể xóa hình ảnh.');
                }
            } else {
                if ($cache_name != '') {
                    $this->cache->delete($cache_name);
                }

                $this->flashSession->success('Xóa hình ảnh thành công.');
            }

            $query = array(
                'id' => $id,
                'module' => $module,
                'page' => $page,
                'q' => $q,
                'cache_name' => $cache_name
            );
            return $this->response->redirect(array('for' => 'category_edit', 'query' => '?' . http_build_query($query)));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @author Vu.Tran
     */
    public function deleteAction() {
        $id = $this->request->getQuery('id', array('int'), -1);
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $cache_name = $this->request->getQuery('cache_name', array('trim'), '');

        $user = $this->session->get('USER');
        if ($user['id'] == $id) {
            throw new Exception('Bạn đang sử dụng tài khoản này.');
        }

        $category = Category::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ));

        if (!$category) {
            throw new Exception('Danh mục này không tồn tại.');
        }

        $category->status = Constant::STATUS_DELETED;

        $this->db->begin();
        try {
            if (!$category->update()) {
                $message = $category->getMessages();
                if (isset($message[0])) {
                    $this->flashSession->error($message[0]->getMessage());
                } else {
                    $this->flashSession->error('Lỗi, không thể xóa.');
                }
            } else {
                if ($cache_name != '') {
                    $this->cache->delete($cache_name);
                }
                
                $this->db->commit();
                $this->flashSession->success('Xóa thành công.');
            }

            $query = array(
                'page' => $page,
                'q' => $q
            );
            return $this->response->redirect(array('for' => 'category', 'query' => '?' . http_build_query($query)));
        } catch (Exception $e) {
            $this->db->rollback();
            throw new Exception($e->getMessage());
        }
    }
    
    /**
     * @author Vu.Tran
     */
    public function attributeGroupAction()
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
            'CategoryController',
            'attributeGroupListAction',
            'AttributeGroupRepository',
            'getListPagination',
             $params
        )));

        $result = $this->cache->get($cache_name);
        if (!$result) {
            $attribute_group_repository = new AttributeGroupRepository();
            $result = $attribute_group_repository->getListPagination($params);
            $this->cache->save($cache_name, $result);
        }
        if ($this->request->isPost()) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }

            if ($this->request->getPost('ordering')) {
                $ordering = $this->request->getPost('ordering');

                if ($ordering && count($ordering) > 0) {
                    $this->db->begin();
                    try {
                        foreach ($ordering as $key => $value) {
                            $attribute_group = AttributeGroup::findFirst($key);
                            if ($attribute_group && count($attribute_group) > 0) {
                                if ($attribute_group->ordering != $value) {
                                    $attribute_group->ordering = (int)$value;
                                    if (!$attribute_group->update()) {
                                        $message = $attribute_group->getMessages();
                                        if (isset($message[0])) {
                                            $this->flashSession->error($message[0]->getMessage());
                                        } else {
                                            $this->flashSession->error('Lỗi, không thể cập nhật.');
                                        }
                                        $this->db->rollback();

                                    }
                                    else {
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
                        return $this->response->redirect(array('for' => 'category_attribute_group', 'query' => '?' . http_build_query($query)));
                    } catch (Exception $e) {
                        throw new Exception('Lỗi hệ thống.');
                    }
                }
            }
        }

        $page_header = 'Nhóm sản phẩm';
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
        $this->view->pick('category/attribute_group');
    }

    /**
     * @author Vu.Tran
     */
    public function attributeGroupAddAction()
    {
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $cache_name = $this->request->getQuery('cache_name', array('trim'), '');

        $attribute_group = new AttributeGroup();
        $form = new AttributeGroupForm($attribute_group);
        if ($this->request->isPost()) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }
            $form->bind($this->request->getPost(), $attribute_group);
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin không hợp lệ.');
            } else {
                $ordering = AttributeGroup::count();
                $attribute_group->name = Util::upperFirstLetter($this->request->getPost('name'));
                $attribute_group->slug = Util::slug($this->request->getPost('name'));
                $attribute_group->ordering = $ordering + 1;
                try {
                    if (!$attribute_group->create()) {
                        $message = $attribute_group->getMessages();
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

                    return $this->response->redirect(array('for' => 'category_attribute_group'));
                } catch (Exception $e) {
                    throw new Exception($e->getMessage());
                }
            }
        }

        $page_header = 'Thêm nhóm sản phẩm';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => 'Danh sách nhóm sản phẩm', 'url' => $this->url->get(array('for' => 'category_attribute_group')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'page' => $page,
            'q' => $q,
            'form' => $form,
            'cache_name' => $cache_name
        ));
        $this->view->pick('category/attribute_group_add');
    }

    /**
     * @author Vu.Tran
     */
    public function attributeGroupEditAction()
    {
        $id = $this->request->getQuery('id', array('int'), -1);
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $cache_name = $this->request->getQuery('cache_name', array('trim'), '');

        $attribute_group = AttributeGroup::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ));

        if (!$attribute_group) {
            throw new Exception('Không có nhóm sản phẩm này.');
        }

        $form = new AttributeGroupForm($attribute_group);
        if ($this->request->isPost()) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }
            $form->bind($this->request->getPost(), $attribute_group);
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin không hợp lệ.');
            } else {
                $ordering = AttributeGroup::count();
                $attribute_group->name = Util::upperFirstLetter($this->request->getPost('name'));
                $attribute_group->slug = Util::slug($this->request->getPost('name'));
                $attribute_group->ordering = $ordering + 1;
                try {
                    if (!$attribute_group->update()) {
                        $message = $attribute_group->getMessages();
                        if (isset($message[0])) {
                            $this->flashSession->error($message[0]->getMessage());
                        } else {
                            $this->flashSession->error('Lỗi, không thể sửa.');
                        }
                    } else {
                        if ($cache_name != '') {
                            $this->cache->delete($cache_name);
                        }

                        $this->flashSession->success('Sửa thành công.');
                    }

                    $query = array(
                        'id' => $id,
                        'page' => $page,
                        'q' => $q,
                        'cache_name' => $cache_name
                    );
                    return $this->response->redirect(array('for' => 'category_attribute_group_edit', 'query' => '?' . http_build_query($query)));
                } catch (Exception $e) {
                    throw new Exception($e->getMessage());
                }
            }
        }

        $page_header = 'Sửa nhóm sản phẩm';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => 'Danh sách nhóm sản phẩm', 'url' => $this->url->get(array('for' => 'category_attribute_group')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'page' => $page,
            'q' => $q,
            'form' => $form,
            'cache_name' => $cache_name
        ));
        $this->view->pick('category/attribute_group_edit');
    }

    /**
     * @author Vu.Tran
     */
    public function attributeGroupDeleteAction()
    {
        $id = $this->request->getQuery('id', array('int'), -1);
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $cache_name = $this->request->getQuery('cache_name', array('trim'), '');

        $attribute_group = AttributeGroup::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ));

        if (!$attribute_group) {
            throw new Exception('Nhóm sản phẩm này không tồn tại.');
        }

        $attribute_group->status = Constant::STATUS_DELETED;

        try {
            if (!$attribute_group->update()) {
                $message = $attribute_group->getMessages();
                if (isset($message[0])) {
                    $this->flashSession->error($message[0]->getMessage());
                } else {
                    $this->flashSession->error('Lỗi, không thể xóa.');
                }
            } else {
                if ($cache_name != '') {
                    $this->cache->delete($cache_name);
                }

                $this->flashSession->success('Xóa thành công.');
            }

            $query = array(
                'page' => $page,
                'q' => $q
            );

            return $this->response->redirect(array('for' => 'category_attribute_group', 'query' => '?' . http_build_query($query)));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @author Vu.Tran
     */
    public function attributeAction()
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
            'CategoryController',
            'attributeAction',
            'AttributeRepository',
            'getListPagination',
             $params
        )));

        $result = $this->cache->get($cache_name);
        if (!$result) {
            $attribute_repository = new AttributeRepository();
            $result = $attribute_repository->getListPagination($params);
            $this->cache->save($cache_name, $result);
        }
        if ($this->request->isPost()) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }

            if ($this->request->getPost('ordering')) {
                $ordering = $this->request->getPost('ordering');

                if ($ordering && count($ordering) > 0) {
                    $this->db->begin();
                    try {
                        foreach ($ordering as $key => $value) {
                            $attribute = Attribute::findFirst($key);
                            if ($attribute && count($attribute) > 0) {
                                if ($attribute->ordering != $value) {
                                    $attribute->ordering = (int)$value;
                                    if (!$attribute->update()) {
                                        $message = $attribute->getMessages();
                                        if (isset($message[0])) {
                                            $this->flashSession->error($message[0]->getMessage());
                                        } else {
                                            $this->flashSession->error('Lỗi, không thể cập nhật.');
                                        }
                                        $this->db->rollback();

                                    }
                                    else {
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
                        return $this->response->redirect(array('for' => 'category_attribute', 'query' => '?' . http_build_query($query)));
                    } catch (Exception $e) {
                        throw new Exception('Lỗi hệ thống.');
                    }
                }
            }
        }

        $page_header = 'Thuộc tính nhóm';
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
        $this->view->pick('category/attribute');
    }

    /**
     * @author Vu.Tran
     */
    public function attributeAddAction()
    {
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $cache_name = $this->request->getQuery('cache_name', array('trim'), '');

        $attribute = new Attribute();
        $form = new AttributeForm($attribute);
        if ($this->request->isPost()) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }
            $form->bind($this->request->getPost(), $attribute);
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin không hợp lệ.');
            } else {
                $ordering = Attribute::count();
                $attribute->name = Util::upperFirstLetter($this->request->getPost('name'));
                $attribute->slug = Util::slug($this->request->getPost('name'));
                $attribute->ordering = $ordering + 1;
                try {
                    if (!$attribute->create()) {
                        $message = $attribute->getMessages();
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

                    return $this->response->redirect(array('for' => 'category_attribute'));
                } catch (Exception $e) {
                    throw new Exception($e->getMessage());
                }
            }
        }

        $page_header = 'Thêm thuộc tính nhóm sản phẩm';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => 'Danh sách thuộc tính nhóm sản phẩm', 'url' => $this->url->get(array('for' => 'category_attribute')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'page' => $page,
            'q' => $q,
            'form' => $form,
            'cache_name' => $cache_name
        ));
        $this->view->pick('category/attribute_add');
    }

    /**
     * @author Vu.Tran
     */
    public function attributeEditAction()
    {
        $id = $this->request->getQuery('id', array('int'), -1);
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $cache_name = $this->request->getQuery('cache_name', array('trim'), '');

        $attribute = Attribute::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ));

        if (!$attribute) {
            throw new Exception('Không có nhóm sản phẩm này.');
        }

        $form = new AttributeForm($attribute);
        if ($this->request->isPost()) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }
            $form->bind($this->request->getPost(), $attribute);
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin không hợp lệ.');
            } else {
                $ordering = AttributeGroup::count();
                $attribute->name = Util::upperFirstLetter($this->request->getPost('name'));
                $attribute->slug = Util::slug($this->request->getPost('name'));
                $attribute->ordering = $ordering + 1;
                try {
                    if (!$attribute->update()) {
                        $message = $attribute->getMessages();
                        if (isset($message[0])) {
                            $this->flashSession->error($message[0]->getMessage());
                        } else {
                            $this->flashSession->error('Lỗi, không thể sửa.');
                        }
                    } else {
                        if ($cache_name != '') {
                            $this->cache->delete($cache_name);
                        }

                        $this->flashSession->success('Sửa thành công.');
                    }

                    $query = array(
                        'id' => $id,
                        'page' => $page,
                        'q' => $q,
                        'cache_name' => $cache_name
                    );
                    return $this->response->redirect(array('for' => 'category_attribute_edit', 'query' => '?' . http_build_query($query)));
                } catch (Exception $e) {
                    throw new Exception($e->getMessage());
                }
            }
        }

        $page_header = 'Sửa thuộc tính sản phẩm';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => 'Danh sách thuộc sản phẩm', 'url' => $this->url->get(array('for' => 'category_attribute')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'page' => $page,
            'q' => $q,
            'form' => $form,
            'cache_name' => $cache_name
        ));
        $this->view->pick('category/attribute_edit');
    }

    /**
     * @author Vu.Tran
     */
    public function attributeDeleteAction()
    {
        $id = $this->request->getQuery('id', array('int'), -1);
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $cache_name = $this->request->getQuery('cache_name', array('trim'), '');

        $attribute = Attribute::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ));

        if (!$attribute) {
            throw new Exception('Nhóm sản phẩm này không tồn tại.');
        }

        $attribute->status = Constant::STATUS_DELETED;

        try {
            if (!$attribute->update()) {
                $message = $attribute->getMessages();
                if (isset($message[0])) {
                    $this->flashSession->error($message[0]->getMessage());
                } else {
                    $this->flashSession->error('Lỗi, không thể xóa.');
                }
            } else {
                if ($cache_name != '') {
                    $this->cache->delete($cache_name);
                }

                $this->flashSession->success('Xóa thành công.');
            }

            $query = array(
                'page' => $page,
                'q' => $q
            );

            return $this->response->redirect(array('for' => 'category_attribute', 'query' => '?' . http_build_query($query)));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @author Vu.Tran
     */
    public function tagAction()
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
            'CategoryController',
            'tagAction',
            'TagRepository',
            'getListPagination',
             $params
        )));

        $result = $this->cache->get($cache_name);
        if (!$result) {
            $tag_repository = new TagRepository();
            $result = $tag_repository->getListPagination($params);
            $this->cache->save($cache_name, $result);
        }
        if ($this->request->isPost()) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }

            if ($this->request->getPost('ordering')) {
                $ordering = $this->request->getPost('ordering');

                if ($ordering && count($ordering) > 0) {
                    $this->db->begin();
                    try {
                        foreach ($ordering as $key => $value) {
                            $tag = Tag::findFirst($key);
                            if ($tag && count($tag) > 0) {
                                if ($tag->ordering != $value) {
                                    $tag->ordering = (int)$value;
                                    if (!$tag->update()) {
                                        $message = $tag->getMessages();
                                        if (isset($message[0])) {
                                            $this->flashSession->error($message[0]->getMessage());
                                        } else {
                                            $this->flashSession->error('Lỗi, không thể cập nhật.');
                                        }
                                        $this->db->rollback();

                                    }
                                    else {
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
                        return $this->response->redirect(array('for' => 'category_attribute_group', 'query' => '?' . http_build_query($query)));
                    } catch (Exception $e) {
                        throw new Exception('Lỗi hệ thống.');
                    }
                }
            }
        }

        $page_header = 'Tag sản phẩm';
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
        $this->view->pick('category/tag');
    }

    /**
     * @author Vu.Tran
     */
    public function tagAddAction()
    {
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $cache_name = $this->request->getQuery('cache_name', array('trim'), '');

        $attribute = new Attribute();
        $form = new AttributeForm($attribute);
        if ($this->request->isPost()) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }
            $form->bind($this->request->getPost(), $attribute);
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin không hợp lệ.');
            } else {
                $ordering = Attribute::count();
                $attribute->name = Util::upperFirstLetter($this->request->getPost('name'));
                $attribute->slug = Util::slug($this->request->getPost('name'));
                $attribute->ordering = $ordering + 1;
                try {
                    if (!$attribute->create()) {
                        $message = $attribute->getMessages();
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

                    return $this->response->redirect(array('for' => 'category_attribute'));
                } catch (Exception $e) {
                    throw new Exception($e->getMessage());
                }
            }
        }

        $page_header = 'Thêm tag';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => 'Danh sách thuộc tính nhóm sản phẩm', 'url' => $this->url->get(array('for' => 'category_attribute')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'page' => $page,
            'q' => $q,
            'form' => $form,
            'cache_name' => $cache_name
        ));
        $this->view->pick('category/tag_add');
    }

    /**
     * @author Vu.Tran
     */
    public function tagEditAction()
    {
        $id = $this->request->getQuery('id', array('int'), -1);
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');
        $cache_name = $this->request->getQuery('cache_name', array('trim'), '');

        $tag = Tag::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ));

        if (!$tag) {
            throw new Exception('Không có tag này.');
        }

        $form = new TagForm($tag);
        if ($this->request->isPost()) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }
            $form->bind($this->request->getPost(), $tag);
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin không hợp lệ.');
            } else {
                $tag->title = Util::upperFirstLetter($this->request->getPost('title'));
                $tag->slug = Util::slug($this->request->getPost('title'));
                $tag->meta_title = Util::niceWordsByChars($this->request->getPost('meta_title'));
                $tag->meta_description = Util::niceWordsByChars($this->request->getPost('meta_description'));
                $tag->meta_keyword = Util::niceWordsByChars($this->request->getPost('meta_keyword'));
                $tag->updated_at = time();
                try {
                    if (!$tag->update()) {
                        $message = $tag->getMessages();
                        if (isset($message[0])) {
                            $this->flashSession->error($message[0]->getMessage());
                        } else {
                            $this->flashSession->error('Lỗi, không thể sửa.');
                        }
                    } else {
                        if ($cache_name != '') {
                            $this->cache->delete($cache_name);
                        }

                        $this->flashSession->success('Sửa thành công.');
                    }

                    $query = array(
                        'id' => $id,
                        'page' => $page,
                        'q' => $q,
                        'cache_name' => $cache_name
                    );
                    
                    return $this->response->redirect(array('for' => 'category_tag_edit', 'query' => '?' . http_build_query($query)));
                } catch (Exception $e) {
                    $this->logger->log('[CategoryController][tagEditAction] ' . $e->getMessage(), Logger::ERROR);
                    throw new Exception($e->getMessage());
                }
            }
        }

        $page_header = 'Sửa tag';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => 'Danh sách tag', 'url' => $this->url->get(array('for' => 'category_tag')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'page' => $page,
            'q' => $q,
            'form' => $form,
            'cache_name' => $cache_name
        ));
        $this->view->pick('category/tag_edit');
    }
}