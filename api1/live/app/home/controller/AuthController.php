<?php
namespace ITECH\Home\Controller;

use Phalcon\Exception;
use ITECH\Home\Controller\BaseController;
use ITECH\Datasource\Lib\Constant;
use ITECH\Datasource\Lib\Util;

class AuthController extends BaseController
{
    /**
     * @author Cuong.Bui
     */
    public function loginUserAction()
    {
        $url = $this->config->auth->request_token_url;
        
        $post = array(
            'channel_id' => $this->config->auth->seeker_channel_id,
            'channel_key' => $this->config->auth->seeker_channel_key,
            'channel_name' => $this->config->auth->seeker_channel_name,
            'referral_url' => $this->request->getQuery('referral_url'),
            'auth_process_url' => $this->request->getQuery('auth_process_url'),
            'signature' => md5($this->config->auth->seeker_channel_id . $this->config->auth->seeker_channel_name . $this->config->auth->seeker_channel_key)
        );

        if ($post['referral_url'] == '') {
            $post['referral_url'] = $this->url->get(array('for' => 'home'));
        }

        if ($post['auth_process_url'] == '') {
            $post['auth_process_url'] = $this->url->get(array('for' => 'login_user_process'));
        }

        if ($this->session->has('USER')) {
            $user = $this->session->get('USER');
            if ($user['type'] == 'Seeker') {
                return $this->response->redirect($post['referral_url']);
            }
        }

        $response = json_decode(Util::curlPost($url, $post), true);
        
        if ($response && isset($response['status']) && $response['status'] == Constant::CODE_SUCCESS) {
            $query = array(
                'channel_id' => $this->config->auth->seeker_channel_id,
                'channel_key' => $this->config->auth->seeker_channel_key,
                'channel_name' => $this->config->auth->seeker_channel_name,
                'token' => isset($response['result']) ? $response['result'] : ''
            );

            $url = $this->config->auth->login_url . '?' . http_build_query($query);
            return $this->response->redirect($url);
        }

        throw new Exception('Lỗi, không thể đăng nhập');
    }

    /**
     * @author Cuong.Bui
     */
    public function loginUserProcessAction()
    {
        $user_id = $this->request->getQuery('user_id');
        $referral_url = $this->request->getQuery('referral_url');

        $url = $this->config->auth->request_info_url;
        $post = array(
            'channel_id' => $this->config->auth->seeker_channel_id,
            'channel_key' => $this->config->auth->seeker_channel_key,
            'channel_name' => $this->config->auth->seeker_channel_name,
            'user_id' => $user_id,
            'token' => $this->request->getQuery('token'),
            'signature' => md5($this->config->auth->seeker_channel_id . $this->config->auth->seeker_channel_name . $this->config->auth->seeker_channel_key . $user_id)
        );

        if ($referral_url == '') {
            $referral_url = $this->url->get(array('for' => 'home'));
        }

        if ($this->session->has('USER')) {
            $user = $this->session->get('USER');
            if ($user['type'] == 'Seeker') {
                return $this->response->redirect($referral_url);
            }
        }

        $response = json_decode(Util::curlPost($url, $post), true);
        if ($response && isset($response['status']) && $response['status'] == Constant::CODE_SUCCESS) {
            $this->session->set('USER', $response['result']);
            $this->cookies->set('USER', serialize($response['result']), strtotime('+4 hours'));

            return $this->response->redirect($referral_url);
        }

        throw new Exception('Lỗi, không thể lấy thông tin tài khoản.');
    }

    /**
     * @author Cuong.Bui
     */
    public function loginEmployerAction()
    {
        $url = $this->config->auth->request_token_url;
        $post = array(
            'channel_id' => $this->config->auth->employer_channel_id,
            'channel_key' => $this->config->auth->employer_channel_key,
            'channel_name' => $this->config->auth->employer_channel_name,
            'referral_url' => $this->request->getQuery('referral_url'),
            'auth_process_url' => $this->request->getQuery('auth_process_url'),
            'signature' => md5($this->config->auth->employer_channel_id . $this->config->auth->employer_channel_name . $this->config->auth->employer_channel_key)
        );

        if ($post['referral_url'] == '') {
            $post['referral_url'] = $this->url->get(array('for' => 'employer_home'));
        }

        if ($post['auth_process_url'] == '') {
            $post['auth_process_url'] = $this->url->get(array('for' => 'login_employer_process'));
        }

        if ($this->session->has('USER')) {
            $user = $this->session->get('USER');
            if ($user['type'] == 'Employer') {
                return $this->response->redirect($post['referral_url']);
            }
        }

        $response = json_decode(Util::curlPost($url, $post), true);
        if ($response && isset($response['status']) && $response['status'] == Constant::CODE_SUCCESS) {
            $query = array(
                'channel_id' => $this->config->auth->employer_channel_id,
                'channel_key' => $this->config->auth->employer_channel_key,
                'channel_name' => $this->config->auth->employer_channel_name,
                'token' => isset($response['result']) ? $response['result'] : ''
            );

            $url = $this->config->auth->login_url . '?' . http_build_query($query);
            return $this->response->redirect($url);
        }

        throw new Exception('Lỗi, không thể đăng nhập');
    }

    /**
     * @author Cuong.Bui
     */
    public function loginEmployerProcessAction()
    {
        $user_id = $this->request->getQuery('user_id');
        $referral_url = $this->request->getQuery('referral_url');

        $url = $this->config->auth->request_info_url;
        $post = array(
            'channel_id' => $this->config->auth->employer_channel_id,
            'channel_key' => $this->config->auth->employer_channel_key,
            'channel_name' => $this->config->auth->employer_channel_name,
            'user_id' => $user_id,
            'token' => $this->request->getQuery('token'),
            'signature' => md5($this->config->auth->employer_channel_id . $this->config->auth->employer_channel_name . $this->config->auth->employer_channel_key . $user_id)
        );

        if ($referral_url == '') {
            $referral_url = $this->url->get(array('for' => 'employer_home'));
        }

        if ($this->session->has('USER')) {
            $user = $this->session->get('USER');
            if ($user['type'] == 'Employer') {
                return $this->response->redirect($referral_url);
            }
        }

        $response = json_decode(Util::curlPost($url, $post), true);
        if ($response && isset($response['status']) && $response['status'] == Constant::CODE_SUCCESS) {
            $this->session->set('USER', $response['result']);
            $this->cookies->set('USER', serialize($response['result']), strtotime('+4 hours'));

            return $this->response->redirect($referral_url);
        }

        throw new Exception('Lỗi, không thể lấy thông tin tài khoản.');
    }
    
    /**
     * @author Vu.Tran
     */
    public function loginAjaxUserAction()
    {
        $url = $this->config->auth->request_token_url;
        
        $post = array(
            'channel_id' => $this->config->auth->user_channel_id,
            'channel_key' => $this->config->auth->user_channel_key,
            'channel_name' => $this->config->auth->user_channel_name,
            'referral_url' => $this->request->getQuery('referral_url'),
            'auth_process_url' => $this->request->getQuery('auth_process_url'),
            'signature' => md5($this->config->auth->user_channel_id . $this->config->auth->user_channel_name . $this->config->auth->seeker_channel_key),
        );

        if ($post['referral_url'] == '') {
            $post['referral_url'] = $this->url->get(array('for' => 'home'));
        }

        if ($post['auth_process_url'] == '') {
            $post['auth_process_url'] = $this->url->get(array('for' => 'login_user_process_ajax'));
        }

        if ($this->session->has('USER')) {
            $user = $this->session->get('USER');
            if ($user['type'] == 'Seeker') {
                return $this->response->redirect($post['referral_url']);
            }
        }

        $response = json_decode(Util::curlPost($url, $post), true);
        if ($response && isset($response['status']) && $response['status'] == Constant::CODE_SUCCESS) {
            $query = array(
                'channel_id' => $this->config->auth->user_channel_id,
                'channel_key' => $this->config->auth->user_channel_key,
                'channel_name' => $this->config->auth->user_channel_name,
                'token' => isset($response['result']) ? $response['result'] : '',
                'email' => $this->request->getPost('email'),
                'password' => $this->request->getPost('password')
            );

            $url = $this->config->auth->login_ajax_url;
            $response = json_decode(Util::curlPost($url, $query), true);
            parent::outputJSON($response);
        }
        
        throw new Exception('Lỗi, không thể đăng nhập');
    }
    
    /**
     * @author Vu.Tran
     */
    public function loginAjaxUserProcessAction()
    {
        $user_id = $this->request->getPost('user_id');
        $url = $this->config->auth->request_info_ajax_url;
        $post = array(
            'channel_id' => $this->config->auth->user_channel_id,
            'channel_key' => $this->config->auth->user_channel_key,
            'channel_name' => $this->config->auth->user_channel_name,
            'user_id' => $user_id,
            'token' => $this->request->getPost('token'),
            'signature' => md5($this->config->auth->user_channel_id . $this->config->auth->user_channel_name . $this->config->auth->user_channel_key . $user_id)
        );

        $response = json_decode(Util::curlPost($url, $post), true);
        parent::outputJSON($response);
    }
}