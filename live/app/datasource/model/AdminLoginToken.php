<?php
namespace ITECH\Datasource\Model;

use ITECH\Datasource\Model\BaseModel;

class AdminLoginToken extends BaseModel
{
    public $id;
    public $admin_id;
    public $otp;
    public $created_at;
    public $expired_at;

    /**
     * @author Cuong.Bui
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('admin_login_token');
    }
}