<?php
namespace ITECH\Datasource\Model;

use ITECH\Datasource\Model\BaseModel;

class Transport extends BaseModel
{
    public $id;	
    public $title; 	
    public $image; 	
    public $description; 		
    public $ordering; 	

    /**
     * @author Vu.Tran
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('transport');
    }
}