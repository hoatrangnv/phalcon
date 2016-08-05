<?php
namespace ITECH\Datasource\Model;

use ITECH\Datasource\Model\BaseModel;

class Payment extends BaseModel
{
    public $id;	
    public $title; 	
    public $image; 	
    public $description; 	
    public $account_id; 	
    public $account_pass; 	
    public $ordering; 	

    /**
     * @author Vu.Tran
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('payment');
    }
}