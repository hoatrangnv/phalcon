<?php
namespace ITECH\Datasource\Model;

use ITECH\Datasource\Model\BaseModel;

class AuthToken extends BaseModel
{
    public $id;
    public $auth_channel_id;
    public $referral_url;
    public $token;
    public $status;
    public $created_at;
    public $updated_at;

    /**
     * @author Cuong.Bui
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('auth_token');
    }
}