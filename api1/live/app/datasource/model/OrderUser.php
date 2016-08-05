<?php
namespace ITECH\Datasource\Model;

use ITECH\Datasource\Model\BaseModel;

class OrderUser extends BaseModel
{
    public $user_id;	
    public $order_id; 	

    /**
     * @author Vu.Tran
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('order_user');
    }
}