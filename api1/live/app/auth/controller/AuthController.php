<?php
namespace ITECH\Auth\Controller;

use Phalcon\Logger;
use Phalcon\Exception;
use ITECH\Auth\Controller\BaseController;
use ITECH\Auth\Form\AdminLoginForm;
use ITECH\Auth\Form\SeekerLoginForm;
use ITECH\Auth\Form\EmployerLoginForm;
use ITECH\Datasource\Model\Admin;
use ITECH\Datasource\Model\AuthToken;
use ITECH\Datasource\Model\User;
use ITECH\Datasource\Model\UserLoginHistory;
use ITECH\Datasource\Model\Employer;
use ITECH\Datasource\Model\EmployerLoginHistory;
use ITECH\Datasource\Lib\Constant;
use ITECH\Datasource\Lib\Util;

class AuthController extends BaseController
{
    /**
     * @author Cuong.Bui
     */
    public function loginAction()
    {
        $channel_id = $this->request->getQuery('channel_id');
        $token = $this->request->getQuery('token');

        switch ($channel_id) {
            case 1:
                $this->view->setMainView('admin');
                $form = new AdminLoginForm();

                $title_for_layout = 'Đăng nhập Người quản trị';
                $view = 'admin/auth/login_admin';
                break;

            case 2:
                $this->view->setMainView('user');
                $form = new EmployerLoginForm();

                $title_for_layout = 'Đăng nhập Nhà tuyển dụng';
                $view = 'user/auth/login_employer';
                break;

            case 3:
                $this->view->setMainView('user');
                $form = new SeekerLoginForm();

                $title_for_layout = 'Đăng nhập Người tìm việc';
                $view = 'user/auth/login_seeker';
                break;

            default:
                throw new Exception('URL không hợp lệ.');
        }

        $auth_token = AuthToken::findFirst(array(
            'conditions' => 'auth_channel_id = :channel_id: AND token = :token: AND status = :status:',
            'bind' => array(
                'channel_id' => $channel_id,
                'token' => $token,
                'status' => Constant::AUTH_TOKEN_STATUS_REQUEST
            )
        ));

        if (!$auth_token || empty($auth_token)) {
            throw new Exception('Token không hợp lệ.');
        }

        if ($this->request->isPost()) {
            if (!$this->security->checkToken()) {
                throw new Exception('Token không chính xác.');
            }

            if (!$form->isValid($this->request->getPost())) {
                $this->flashSession->error('Thông tin không hợp lệ.');
            } else {
                switch ($channel_id) {
                    case 1:
                        $username = $this->request->getPost('username');
                        $password = md5($this->request->getPost('password'));

                        $admin = self::adminLogin($username, $password);
                        if (!$admin) {
                            $this->flashSession->error('Tài khoản đăng nhập không chính xác.');
                        } else {
                            $auth_token->status = Constant::AUTH_TOKEN_STATUS_LOGINED;
                            $auth_token->updated_at = date('Y-m-d H:i:s');

                            $admin->logined_at = date('Y-m-d H:i:s');
                            $admin->logined_ip = $this->request->getClientAddress();

                            $this->db->begin();
                            try {
                                $auth_token->update();
                                $admin->update();
                                $this->db->commit();

                                $query = array(
                                    'channel_id' => $channel_id,
                                    'user_id' => $admin->id,
                                    'token' => $auth_token->token,
                                    'referral_url' => $auth_token->referral_url
                                );
                                $url = $auth_token->auth_process_url . '?' . http_build_query($query);

                                return $this->response->redirect($url);
                            } catch (Exception $e) {
                                $this->db->rollback();

                                $this->logger->log('[AuthController][loginAction] ' . $e->getMessage(), Logger::ERROR);
                                throw new Exception($e->getMessage());
                            }
                        }
                        break;

                    case 2:
                        $email = $this->request->getPost('email');
                        $password = md5($this->request->getPost('password'));

                        $employer = self::employerLogin($email, $password);
                        if (!$employer) {
                            $this->flashSession->error('Tài khoản đăng nhập không chính xác.');
                        } else {
                            $auth_token->status = Constant::AUTH_TOKEN_STATUS_LOGINED;
                            $auth_token->updated_at = date('Y-m-d H:i:s');

                            $employer->slug = Util::slug($employer->name);
                            $employer->logined_at = date('Y-m-d H:i:s');
                            $employer->logined_ip = $this->request->getClientAddress();

                            $employer_login_history = new EmployerLoginHistory();
                            $employer_login_history->employer_id = $employer->id;
                            $employer_login_history->browsers = $this->request->getUserAgent();
                            $employer_login_history->logined_at = date('Y-m-d H:i:s');
                            $employer_login_history->logined_ip = $this->request->getClientAddress();

                            $this->db->begin();
                            try {
                                $auth_token->update();
                                $employer->update();
                                $employer_login_history->create();
                                $this->db->commit();

                                $query = array(
                                    'channel_id' => $channel_id,
                                    'user_id' => $employer->id,
                                    'token' => $auth_token->token,
                                    'referral_url' => $auth_token->referral_url
                                );
                                $url = $auth_token->auth_process_url . '?' . http_build_query($query);

                                return $this->response->redirect($url);
                            } catch (Exception $e) {
                                $this->db->rollback();

                                $this->logger->log('[AuthController][loginAction] ' . $e->getMessage(), Logger::ERROR);
                                throw new Exception($e->getMessage());
                            }
                        }
                        break;

                    case 3:
                        $email = $this->request->getPost('email');
                        $password = md5($this->request->getPost('password'));

                        $seeker = self::userLogin($email, $password);
                        if (!$seeker) {
                            $this->flashSession->error('Tài khoản đăng nhập không chính xác.');
                        } else {
                            $auth_token->status = Constant::AUTH_TOKEN_STATUS_LOGINED;
                            $auth_token->updated_at = date('Y-m-d H:i:s');

                            $seeker->slug = Util::slug($seeker->name);
                            $seeker->logined_at = date('Y-m-d H:i:s');
                            $seeker->logined_ip = $this->request->getClientAddress();

                            $seeker_login_history = new SeekerLoginHistory();
                            $seeker_login_history->seeker_id = $seeker->id;
                            $seeker_login_history->browsers = $this->request->getUserAgent();
                            $seeker_login_history->logined_at = date('Y-m-d H:i:s');
                            $seeker_login_history->logined_ip = $this->request->getClientAddress();

                            $this->db->begin();
                            try {
                                $auth_token->update();
                                $seeker->update();
                                $seeker_login_history->create();
                                $this->db->commit();

                                $query = array(
                                    'channel_id' => $channel_id,
                                    'user_id' => $seeker->id,
                                    'token' => $auth_token->token,
                                    'referral_url' => $auth_token->referral_url
                                );
                                $url = $auth_token->auth_process_url . '?' . http_build_query($query);

                                return $this->response->redirect($url);
                            } catch (Exception $e) {
                                $this->db->rollback();

                                $this->logger->log('[AuthController][loginAction] ' . $e->getMessage(), Logger::ERROR);
                                throw new Exception($e->getMessage());
                            }
                        }
                        break;
                }
            }
        }

        $this->view->setVars(array(
            'title_for_layout' => $title_for_layout,
            'form' => $form
        ));
        $this->view->pick($view);
    }
    
    /**
     * @author Vu.Tran
     */
    public function loginAjaxAction()
    {
        $response = array();

        $response['status'] = Constant::CODE_SUCCESS;
        $response['message'] = 'Success.';
        
        $channel_id = $this->request->getPost('channel_id');
        $token = $this->request->getPost('token');

        $auth_token = AuthToken::findFirst(array(
            'conditions' => 'auth_channel_id = :channel_id: AND token = :token: AND status = :status:',
            'bind' => array(
                'channel_id' => $channel_id,
                'token' => $token,
                'status' => Constant::AUTH_TOKEN_STATUS_REQUEST
            )
        ));
        
        if (!$auth_token || empty($auth_token)) {
            throw new Exception('Token không hợp lệ.');
        }
        
        if (!$this->request->isPOST()) {
            $response['status'] = Constant::CODE_ERROR;
            $response['message'] = 'Invalid POST method.';
        } else {
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password'); 
            $user = self::userLogin($email, $password);
            if (!$user) {
                $response['status'] = Constant::CODE_ERROR; 
                $response['message'] = 'Tài khoản đăng nhập không chính xác.';
            } else {
                $auth_token->status = Constant::AUTH_TOKEN_STATUS_LOGINED;
                $auth_token->updated_at = date('Y-m-d H:i:s');

                $user->slug = Util::slug($user->name);
                $user->logined_at = date('Y-m-d H:i:s');
                $user->logined_ip = $this->request->getClientAddress();

                $user_login_history = new UserLoginHistory();
                $user_login_history->seeker_id = $user->id;
                $user_login_history->browsers = $this->request->getUserAgent();
                $user_login_history->logined_at = date('Y-m-d H:i:s');
                $user_login_history->logined_ip = $this->request->getClientAddress();

                $this->db->begin();
                try {
                    $auth_token->update();
                    $user->update();
                    $user_login_history->create();
                    $this->db->commit();

                    $query = array(
                        'channel_id' => $channel_id,
                        'user_id' => $user->id,
                        'token' => $auth_token->token,
                        'referral_url' => $auth_token->referral_url
                    );
                    $url = $auth_token->auth_process_url . '?' . http_build_query($query);
                    $response = json_decode(Util::curlPost($url, $query), true);
                } catch (Exception $e) {
                    $this->db->rollback();

                    $this->logger->log('[AuthController][loginAction] ' . $e->getMessage(), Logger::ERROR);
                    throw new Exception($e->getMessage());
                }
            }
        }
        parent::outputJSON($response);
        
    }
    
    /**
     * @author Vu.Tran
     */
    public function requestInfoAjaxAction()
    {
        $response = array();

        $response['status'] = Constant::CODE_SUCCESS;
        $response['message'] = 'Success.';

        if (!$this->request->isPOST()) {
            $response['status'] = Constant::CODE_ERROR;
            $response['message'] = 'Invalid POST method.';
        } else {
            $data = array(
                'channel_id' => $this->request->getPost('channel_id'),
                'channel_name' => $this->request->getPost('channel_name'),
                'channel_key' => $this->request->getPost('channel_key'),
                'user_id' => $this->request->getPost('user_id'),
                'token' => $this->request->getPost('token'),
                'signature' => $this->request->getPost('signature')
            );
            
            $signature = md5($data['channel_id'] . $data['channel_name'] . $data['channel_key'] . $data['user_id']);
            if ($signature != $data['signature']) {
                $response['status'] = Constant::CODE_ERROR;
                $response['message'] = 'Signature is invalid.';
            } else {
                $auth_token = AuthToken::findFirst(array(
                    'conditions' => 'auth_channel_id = :channel_id: AND token = :token: AND status = :status_logined:',
                    'bind' => array(
                        'channel_id' => $data['channel_id'],
                        'token' => $data['token'],
                        'status_logined' => Constant::AUTH_TOKEN_STATUS_LOGINED
                    )
                ));

                if (!$auth_token || empty($auth_token)) {
                    $response['status'] = Constant::CODE_ERROR;
                    $response['message'] = 'Token is invalid.';
                } else {
                    $seeker = User::findFirst(array(
                        'conditions' => 'id = :user_id: AND status = :status:',
                        'bind' => array(
                            'user_id' => $data['user_id'],
                            'status' => Constant::USER_STATUS_ACTIVED
                        )
                    ));

                    if (!$seeker) {
                        $response['status'] = Constant::CODE_ERROR;
                        $response['message'] = 'No user found.';
                    } else {
                        $response['message'] = 'Đăng nhập thành công';
                        $response['result'] = array(
                            'id' => $seeker->id,
                            'email' => $seeker->email,
                            'name' => $seeker->name,
                            'avatar' => $seeker->avatar,
                            'type' => 'User'
                        );
                    }
                }
            }
        }

        parent::outputJSON($response);
    }

    /**
     * @author Vu.Tran
     */
    public function requestInfoAction()
    {
        $response = array();

        $response['status'] = Constant::CODE_SUCCESS;
        $response['message'] = 'Success.';

        if (!$this->request->isPOST()) {
            $response['status'] = Constant::CODE_ERROR;
            $response['message'] = 'Invalid POST method.';
        } else {
            $data = array(
                'channel_id' => $this->request->getPost('channel_id'),
                'channel_name' => $this->request->getPost('channel_name'),
                'channel_key' => $this->request->getPost('channel_key'),
                'user_id' => $this->request->getPost('user_id'),
                'token' => $this->request->getPost('token'),
                'signature' => $this->request->getPost('signature')
            );
            
            $signature = md5($data['channel_id'] . $data['channel_name'] . $data['channel_key'] . $data['user_id']);
            if ($signature != $data['signature']) {
                $response['status'] = Constant::CODE_ERROR;
                $response['message'] = 'Signature is invalid.';
            } else {
                $auth_token = AuthToken::findFirst(array(
                    'conditions' => 'auth_channel_id = :channel_id: AND token = :token: AND status = :status_logined:',
                    'bind' => array(
                        'channel_id' => $data['channel_id'],
                        'token' => $data['token'],
                        'status_logined' => Constant::AUTH_TOKEN_STATUS_LOGINED
                    )
                ));

                if (!$auth_token || empty($auth_token)) {
                    $response['status'] = Constant::CODE_ERROR;
                    $response['message'] = 'Token is invalid.';
                } else {
                    switch ($data['channel_id']) {
                        case 1:
                            $admin = Admin::findFirst(array(
                                'conditions' => 'id = :user_id: AND type <> :type_inactived:',
                                'bind' => array(
                                    'user_id' => $data['user_id'],
                                    'type_inactived' => Constant::ADMIN_TYPE_INACTIVED
                                )
                            ));
                            if (!$admin) {
                                $response['status'] = Constant::CODE_ERROR;
                                $response['message'] = 'No user found.';
                            } else {
                                $response['result'] = array(
                                    'id' => $admin->id,
                                    'username' => $admin->username,
                                    'email' => $admin->email,
                                    'name' => $admin->name,
                                    'type' => $admin->type
                                );
                            }
                            break;

                        case 2:
                            $employer = Employer::findFirst(array(
                                'conditions' => 'id = :user_id: AND status = :status:',
                                'bind' => array(
                                    'user_id' => $data['user_id'],
                                    'status' => Constant::EMPLOYER_STATUS_ACTIVED
                                )
                            ));

                            if (!$employer) {
                                $response['status'] = Constant::CODE_ERROR;
                                $response['message'] = 'No user found.';
                            } else {
                                $response['result'] = array(
                                    'id' => $employer->id,
                                    'email' => $employer->email,
                                    'name' => $employer->name,
                                    'is_premium' => $employer->is_premium,
                                    'is_premium_search_only' => $employer->is_premium_search_only,
                                    'premium_expired_at' => $employer->premium_expired_at,
                                    'cover_status' => $employer->cover_status,
                                    'type' => 'Employer'
                                );
                            }
                            break;

                        case 3:
                            $seeker = User::findFirst(array(
                                'conditions' => 'id = :user_id: AND status = :status:',
                                'bind' => array(
                                    'user_id' => $data['user_id'],
                                    'status' => Constant::SEEKER_STATUS_ACTIVED
                                )
                            ));

                            if (!$seeker) {
                                $response['status'] = Constant::CODE_ERROR;
                                $response['message'] = 'No user found.';
                            } else {
                                $response['result'] = array(
                                    'id' => $seeker->id,
                                    'email' => $seeker->email,
                                    'name' => $seeker->name,
                                    'is_premium' => $seeker->is_premium,
                                    'premium_expired_at' => $seeker->premium_expired_at,
                                    'cover_status' => $seeker->cover_status,
                                    'type' => 'Seeker'
                                );
                            }
                            break;
                    }
                }
            }
        }

        parent::outputJSON($response);
    }

    /**
     * @author Cuong.Bui
     */
    private function adminLogin($username, $password)
    {
        $admin = Admin::findFirst(array(
            'conditions' => 'username = :username: AND password = :password: AND type <> :type_inactived:',
            'bind' => array(
                'username' => $username,
                'password' => $password,
                'type_inactived' => Constant::ADMIN_TYPE_INACTIVED
            )
        ));

        return $admin;
    }

    /**
     * @author Vu.Tran
     */
    private function userLogin($email, $password)
    {
        $user = User::findFirst(array(
            'conditions' => 'email = :email: AND password = :password: AND status = :status:',
            'bind' => array(
                'email' => $email,
                'password' => $password,
                'status' => Constant::USER_STATUS_ACTIVED
            )
        ));

        return $user;
    }

    /**
     * @author Cuong.Bui
     */
    private function employerLogin($email, $password)
    {
        $employer = Employer::findFirst(array(
            'conditions' => 'email = :email: AND password = :password: AND status = :status:',
            'bind' => array(
                'email' => $email,
                'password' => $password,
                'status' => Constant::EMPLOYER_STATUS_ACTIVED
            )
        ));

        return $employer;
    }
}