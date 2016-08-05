<?php
namespace ITECH\Admin\Controller;

use ITECH\Admin\Controller\BaseController;
use ITECH\Datasource\Lib\Util;
use ITECH\Datasource\Lib\Constant;
use Phalcon\Exception;

class AuthController extends BaseController
{
    /**
     * @author Cuong.Bui
     */
    public function loginAction()
    {
        $url = $this->config->auth->request_token_url;
        $post = array(
            'channel_id' => $this->config->auth->channel_id,
            'channel_key' => $this->config->auth->channel_key,
            'channel_name' => $this->config->auth->channel_name,
            'referral_url' => $this->request->getQuery('referral_url'),
            'auth_process_url' => $this->request->getQuery('auth_process_url'),
            'signature' => md5($this->config->auth->channel_id . $this->config->auth->channel_name . $this->config->auth->channel_key),
        );

        if ($post['referral_url'] == '') {
            $post['referral_url'] = $this->url->get(array('for' => 'home'));
        }

        if ($post['auth_process_url'] == '') {
            $post['auth_process_url'] = $this->url->get(array('for' => 'auth_process'));
        }

        if ($this->session->has('USER')) {
            return $this->response->redirect($post['referral_url']);
        }

        $response = json_decode(Util::curlPost($url, $post), true);
        //var_dump($response); exit();
        if ($response && isset($response['status']) && $response['status'] == Constant::CODE_SUCCESS) {
            $query = array(
                'channel_id' => $this->config->auth->channel_id,
                'channel_key' => $this->config->auth->channel_key,
                'channel_name' => $this->config->auth->channel_name,
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
    public function processAction()
    {
        $user_id = $this->request->getQuery('user_id');
        $referral_url = $this->request->getQuery('referral_url');
        
        $url = $this->config->auth->request_info_url;
        $post = array(
            'channel_id' => $this->config->auth->channel_id,
            'channel_key' => $this->config->auth->channel_key,
            'channel_name' => $this->config->auth->channel_name,
            'user_id' => $user_id,
            'token' => $this->request->getQuery('token'),
            'signature' => md5($this->config->auth->channel_id . $this->config->auth->channel_name . $this->config->auth->channel_key . $user_id),
        );

        if ($referral_url == '') {
            $referral_url = $this->url->get(array('for' => 'home'));
        }

        if ($this->session->has('USER')) {
            return $this->response->redirect($referral_url);
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
    public function logoutAction()
    {
        $this->session->remove('USER');
        $this->session->remove('action');
        $this->cookies->set('USER', null);

        return $this->response->redirect(array('for' => 'home'));
    }
}