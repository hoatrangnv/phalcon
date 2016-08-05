<?php
namespace TVN\Auth\Controller;

use TVN\Auth\Controller\BaseController;
use TVN\Datasource\Lib\Constant;
use TVN\Datasource\Lib\Util;
use TVN\Datasource\Model\AuthChannel;
use TVN\Datasource\Model\AuthToken;

class TokenController extends BaseController
{
    /**
     * @author Cuong.Bui
     */
    public function requestTokenAction()
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
                'referral_url' => $this->request->getPost('referral_url'),
                'auth_process_url' => $this->request->getPost('auth_process_url'),
                'signature' => $this->request->getPost('signature')
            );

            $signature = md5($data['channel_id'] . $data['channel_name'] . $data['channel_key']);
            if ($signature != $data['signature']) {
                $response['status'] = Constant::CODE_ERROR;
                $response['message'] = 'Signature is invalid.';
            } else {
                $channel = AuthChannel::findFirst(array(
                    'conditions' => 'id = :id: AND name = :name: AND key = :key:',
                    'bind' => array(
                        'id' => $data['channel_id'],
                        'name' => $data['channel_name'],
                        'key' => $data['channel_key']
                    )
                ));

                if (!$channel || empty($channel)) {
                    $response['status'] = Constant::CODE_ERROR;
                    $response['message'] = 'Channel not found or Key is invalid.';
                } else {
                    $token = Util::token();

                    $auth_token = new AuthToken();
                    $auth_token->auth_channel_id = $channel->id;
                    $auth_token->referral_url = $data['referral_url'];
                    $auth_token->auth_process_url = $data['auth_process_url'];
                    $auth_token->token = $token;
                    $auth_token->status = Constant::AUTH_TOKEN_STATUS_REQUEST;
                    $auth_token->created_at = date('Y-m-d H:i:s');
                    $auth_token->updated_at = null;

                    if (!$auth_token->create()) {
                        $response = array(
                            'status' => Constant::CODE_ERROR,
                            'message' => 'Error, cannot create token.'
                        );
                    } else {
                        $response['result'] = $token;
                    }
                }
            }
        }

        parent::outputJSON($response);
    }
}