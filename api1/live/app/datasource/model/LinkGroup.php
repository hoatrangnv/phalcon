<?php
namespace ITECH\Datasource\Model;

use ITECH\Datasource\Model\BaseModel;

class LinkGroup extends BaseModel
{
    public $id; 
    public $name; 	
    public $slug; 	
    public $ordering; 

    /**
     * @author Vu.Tran
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('link_group'); 
        
        $this->hasMany('id', 'ITECH\Datasource\Model\Link', 'group_id', array(
            'alias' => 'Link', 
            'reusable' => true  
        ));
    }    
}