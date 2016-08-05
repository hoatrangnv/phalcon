<?php
namespace ITECH\Admin\Controller;

use Phalcon\Exception;
use ITECH\Admin\Controller\BaseController;
use ITECH\Admin\Form\AdminUserForm;
use ITECH\Datasource\Model\Admin;
use ITECH\Datasource\Model\UserCategory;
use ITECH\Datasource\Model\Category;
use ITECH\Datasource\Repository\AdminRepository;
use ITECH\Datasource\Repository\CategoryRepository;
use ITECH\Admin\Component\CategoryComponent;
use ITECH\Datasource\Lib\Util;
use ITECH\Datasource\Lib\Constant;

class UserController extends BaseController
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
     * @author Cuong.Bui
     */
    public function indexAction()
    {
        $page = $this->request->getQuery('page', array('int'), 1);
        $limit = $this->config->application->pagination_limit;
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');

        $params = array(
            'conditions' => array('q' => $q),
            'page' => (int)$page,
            'limit' => (int)$limit
        );

        $admin_repository = new AdminRepository();
        $result = $admin_repository->getListPagination($params);

        $page_header = 'Thành viên';
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
            'result' => $result
        ));
        $this->view->pick('user/index');
    }

    /**
     * @author Cuong.Bui
     */
    public function addAction()
    {
        $admin = new Admin();
        $form = new AdminUserForm();

        if ($this->request->isPost()) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }

            $form->bind($this->request->getPost(), $admin);
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin không hợp lệ.');
            } else {
                $admin->name = Util::upperFirstLetters($this->request->getPost('name'));
                $admin->phone = Util::numberOnly($this->request->getPost('phone'));
                $admin->password = md5($this->request->getPost('password'));
                $admin->created_at = date('Y-m-d H:i:s');
                $admin->birthday = date('Y-m-d H:i:s');

                try {
                    if (!$admin->create()) {
                        $message = $admin->getMessages();
                        exit();
                        if (isset($message[0])) {
                            $this->flashSession->error($message[0]->getMessage());
                        } else {
                            $this->flashSession->error('Lỗi, không thể thêm.');
                        }
                    } else {
                        $this->flashSession->success('Thêm thành công.');
                    }
                } catch (Exception $e) {
                    throw new Exception($e->getMessage());
                }
            }
        }

        $page_header = 'Thêm thành viên';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => 'Thành viên', 'url' => $this->url->get(array('for' => 'user_list')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'form' => $form
        ));
        $this->view->pick('user/add');
    }

    /**
     * @author Cuong.Bui
     */
    public function editAction()
    {
        $id = $this->request->getQuery('id', array('int'), -1);
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');

        $user = $this->session->get('USER');
        if ($user['id'] == $id) {
            return $this->response->redirect(array('for' => 'profile'));
        }

        $admin = Admin::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ));

        if (!$admin) {
            throw new Exception('Tài khoản này không tồn tại.');
        }

        $user_categories = UserCategory::find(array(
            'conditions' => 'user_id = :id:',
            'bind' => array('id' => $id)
        ));

        $in_array = array();
        foreach ($user_categories as $item) {
            $in_array[] = $item->category_id; 
        }

        $category_component = new CategoryComponent();

        $params = array(
            'conditions' => array(
                'module' => Constant::MODULE_ARTICLES,
                'parent_id' => intval(0)
            ),
        );
        $cache_categories = md5(serialize(array(
            'ArticleController',
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
     
        $params = array();
        $category_layout_cache_name = md5(serialize(array(
            'UserController',
            'editAction',
            $id,
            $params
        )));
        
        $category_layout = $this->cache->get($category_layout_cache_name);
        if (!$category_layout) {
            $category_layout = '';
            $level = '';
            foreach ($categories as $item) {

                $active = '';
                if (in_array($item->id, $in_array)) {
                    $active = 'checked="checked"';
                }

                $category_layout .= '<div class="checkbox">' . '<input type="checkbox" name="category[]" value="' . $item->id . '" class="red" ' . $active . '>' . $item->name . '</div>';
                $params = array(
                    'conditions' => array(
                        'module' => Constant::MODULE_ARTICLES,
                        'parent_id' => $item->id,
                    ),
                );
                $sub_category_layout = '';
                $category_layout .= $category_component->sub_checkbox($params, $sub_category_layout, $level, $in_array);

            }
            $this->cache->save($category_layout_cache_name, $category_layout);
        }

        $old_username = $admin->username;

        $form = new AdminUserForm($admin, array('edit' => true));
        if ($this->request->isPost()) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }

            $form->bind($this->request->getPost(), $admin);
            if (!$form->isValid()) {
                $this->flashSession->error('Thông tin không hợp lệ.');
            } else {
                $new_username = $this->request->getPost('username');

                $admin->name = Util::upperFirstLetters($this->request->getPost('name'));
                $admin->phone = Util::numberOnly($this->request->getPost('phone'));
                $admin->type = $this->request->getPost('type');
                $admin->updated_at = date('Y-m-d H:i:s');

                $ac = $this->request->getPost('category');
                if ($user_categories) {
                    foreach ($user_categories as $user_category) {
                        if (!in_array($user_category->category_id, $ac)) {
                            if (!$user_category->delete()) {
                                $message = $user_category->getMessages();
                                if (isset($message[0])) {
                                    $this->flashSession->error($message[0]->getMessage());
                                } else {
                                    $this->flashSession->error('Lỗi, không thể cập nhật danh mục bài viết.');
                                }
                            }
                        } else {
                            foreach ($ac as $key => $item) {
                                if ($user_category->category_id == $item) {
                                    unset($ac[$key]);
                                } 
                            }
                        }
                    }
                    
                }

                foreach ($ac as $item) {
                    $user_category = new UserCategory();
                    $user_category->user_id = $id;
                    $user_category->category_id = $item;
                    if (!$user_category->create()) {
                        $message = $user_category->getMessages();
                        var_dump($message[0]);
                        if (isset($message[0])) {
                            $this->flashSession->error($message[0]->getMessage());
                        } else {
                            $this->flashSession->error('Lỗi, không thể cập nhật danh mục bài viết.');
                        }
                    }
                }

                if ($this->request->getPost('new_password') != '') {
                    $admin->password = md5($this->request->getPost('new_password'));
                }

                if ($admin->type == Constant::ADMIN_TYPE_INACTIVED) {
                    $new_username = '';
                }

                $this->db->begin();
                try {
                    if (!$admin->update()) {
                        $message = $admin->getMessages();
                        if (isset($message[0])) {
                            $this->flashSession->error($message[0]->getMessage());
                        } else {
                            $this->flashSession->error('Lỗi, không thể cập nhật.');
                        }
                    } else {
                        if ($new_username == '') {
                            $params = array(
                                'old_username' => $old_username,
                                'new_username' => $new_username
                            );
                        }

                        $this->db->commit();
                        $this->flashSession->success('Cập nhật thành công.');
                    }

                    $query = array(
                        'id' => $id,
                        'page' => $page,
                        'q' => $q
                    );
                    return $this->response->redirect(array('for' => 'user_edit', 'query' => '?' . http_build_query($query)));
                } catch (Exception $e) {
                    $this->db->rollback();
                    throw new Exception($e->getMessage());
                }
            }
        }
       
        $page_header = 'Chỉnh sửa thành viên';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => 'Thành viên', 'url' => $this->url->get(array('for' => 'user_list')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'page' => $page,
            'q' => $q,
            'form' => $form,
            'admin' => $admin,
            'category_layout' => $category_layout
        ));
        $this->view->pick('user/edit');
    }

    /**
     * @author Cuong.Bui
     */
    public function deleteAction()
    {
        $id = $this->request->getQuery('id', array('int'), -1);
        $page = $this->request->getQuery('page', array('int'), 1);
        $q = $this->request->getQuery('q', array('striptags', 'trim'), '');

        $user = $this->session->get('USER');
        if ($user['id'] == $id) {
            throw new Exception('Bạn đang sử dụng tài khoản này.');
        }

        $admin = Admin::findFirst(array(
            'conditions' => 'id = :id:',
            'bind' => array('id' => $id)
        ));

        if (!$admin) {
            throw new Exception('Tài khoản này không tồn tại.');
        }

        $admin->type = Constant::ADMIN_TYPE_INACTIVED;

        $admin->total_employers = null;
        $admin->total_premium_employers = null;
        $admin->total_seekers = null;
        $admin->total_premium_seekers = null;

        $admin->is_leader = Constant::ADMIN_IS_MEMBER;
        $admin->leader_id = null;
        $admin->area = null;
        $admin->updated_at = date('Y-m-d H:i:s');

        $this->db->begin();
        try {
            if (!$admin->update()) {
                $message = $admin->getMessages();
                if (isset($message[0])) {
                    $this->flashSession->error($message[0]->getMessage());
                } else {
                    $this->flashSession->error('Lỗi, không thể xóa.');
                }
            } else {
                $params = array(
                    'old_username' => $admin->username,
                    'new_username' => ''
                );

                $this->db->commit();
                $this->flashSession->success('Xóa thành công.');
            }

            $query = array(
                'page' => $page,
                'q' => $q
            );
            return $this->response->redirect(array('for' => 'user_list', 'query' => '?' . http_build_query($query)));
        } catch (Exception $e) {
            $this->db->rollback();
            throw new Exception($e->getMessage());
        }
    }
}