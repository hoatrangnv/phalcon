<?php
namespace ITECH\Admin\Controller;

use Phalcon\Exception;
use ITECH\Admin\Controller\BaseController;
use ITECH\Admin\Form\AdminProfileForm;
use ITECH\Admin\Form\AdminChangePasswordForm;
use ITECH\Datasource\Model\Admin;
use ITECH\Datasource\Lib\Util;

class AdminController extends BaseController
{
    /**
     * @author Cuong.Bui
     */
    public function initialize()
    {
        parent::initialize();
        parent::authenticate();
    }

    /**
     * @author Cuong.Bui
     */
    public function profileAction()
    {
        $countvisitor = 0;
        if($this->session->get('action')){
            $countvisitor = $this->session->get('action') + 1;
        }
        $this->session->set('action',$countvisitor);



        $user = $this->session->get('USER');
        $admin = Admin::findFirst(array(
            'conditions' => 'id = :id: AND type = :type:',
            'bind' => array(
                'id' => $user['id'],
                'type' => $user['type']
            )
        ));

        if (!$admin) {
            throw new Exception('Tài khoản này không tồn tại.');
        }

        $session = array(
            'id' => $admin->id,
            'username' => $admin->username,
            'email' => $admin->email,
            'name' => $admin->name,
            'type' => $admin->type,
            //'is_leader' => $admin->is_leader,
            //'leader_id' => $admin->leader_id,
            //'area' => $admin->area
        );
        $this->session->set('USER', $session);
        $this->cookies->set('USER', serialize($session), strtotime('+4 hours'));

        $form = new AdminProfileForm($admin);//print_r($form);die;
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
                $admin->updated_at = date('Y-m-d H:i:s');

                try {
                    if (!$admin->update()) {
                        $message = $admin->getMessages();
                        if (isset($message[0])) {
                            $this->flashSession->error($message[0]->getMessage());
                        } else {
                            $this->flashSession->error('Lỗi, không thể cập nhật.');
                        }
                    } else {
                        $this->flashSession->success('Cập nhật thành công.');
                    }

                    return $this->response->redirect(array('for' => 'profile'));
                } catch (Exception $e) {
                    throw new Exception($e->getMessage());
                }
            }
        }

        $page_header = 'Tài khoản';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard'.$this->session->get('action'), 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'form' => $form
        ));
        $this->view->pick('admin/profile');
    }

    /**
     * @author Cuong.Bui
     */
    public function changePasswordAction()
    {
        $user = $this->session->get('USER');
        $admin = Admin::findFirst(array(
            'conditions' => 'id = :id: AND type = :type:',
            'bind' => array(
                'id' => $user['id'],
                'type' => $user['type']
            )
        ));

        if (!$admin) {
            throw new Exception('Tài khoản này không tồn tại.');
        }

        $form = new AdminChangePasswordForm();
        if ($this->request->isPost()) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }

            if (!$form->isValid($this->request->getPost())) {
                $this->flashSession->error('Thông tin không hợp lệ.');
            } else {
                $old_password = md5($this->request->getPost('old_password'));
                $new_password = md5($this->request->getPost('new_password'));

                if ($old_password != $admin->password) {
                    $this->flashSession->error('Mật khẩu cũ không chính xác.');
                } else {
                    $admin->password = $new_password;
                    $admin->updated_at = date('Y-m-d H:i:s');

                    try {
                        if (!$admin->update()) {
                            $message = $admin->getMessages();
                            if (isset($message[0])) {
                                $this->flashSession->error($message[0]->getMessage());
                            } else {
                                $this->flashSession->error('Lỗi, không thể cập nhật.');
                            }
                        } else {
                            $this->flashSession->success('Đổi mật khẩu thành công.');
                        }

                        return $this->response->redirect(array('for' => 'profile_change_password'));
                    } catch (Exception $e) {
                        throw new Exception($e->getMessage());
                    }
                }
            }
        }

        $page_header = 'Đổi mật khẩu';
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => 'Dashboard', 'url' => $this->url->get(array('for' => 'home')));
        $breadcrumbs[] = array('title' => 'Tài khoản', 'url' => $this->url->get(array('for' => 'profile')));
        $breadcrumbs[] = array('title' => $page_header, 'url' => '');

        $this->view->setVars(array(
            'page_header' => $page_header,
            'breadcrumbs' => $breadcrumbs,
            'form' => $form
        ));
        $this->view->pick('admin/change_password');
    }
}