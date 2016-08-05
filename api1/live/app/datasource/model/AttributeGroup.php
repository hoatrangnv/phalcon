<?php
namespace ITECH\Datasource\Model;

use ITECH\Datasource\Model\BaseModel;

class AttributeGroup extends BaseModel
{ 
    public $id;	
    public $name;
    public $slug;
    public $ordering;
    public $status;

    /**
     * @author Vu.Tran
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('attribute_group');  
    }    
}