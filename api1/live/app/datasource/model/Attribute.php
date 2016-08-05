<?php
namespace ITECH\Datasource\Model;

use ITECH\Datasource\Model\BaseModel;

class Attribute extends BaseModel
{ 
    public $id;	
    public $name;
    public $slug;
    public $type;
    public $ordering;
    public $attribute_group_id;
    public $status;
    

    /**
     * @author Vu.Tran
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('attribute');  
    }    
}