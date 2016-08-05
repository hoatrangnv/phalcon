<?php
namespace ITECH\Datasource\Model;

use ITECH\Datasource\Model\BaseModel;

class UserLoginHistory extends BaseModel
{
    public $id;
    public $user_id;
    public $browsers;
    public $logined_at;
    public $logined_ip;

    /**
     * @author Vu.Tran
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('user_login_history');

        $this->belongsTo('user_id', 'ITECH\Datasource\Model\User', 'id', array(
            'alias' => 'User',
            'foreignKey' => true
        ));
    }
}