<?php
namespace ITECH\Datasource\Model;

use ITECH\Datasource\Model\BaseModel;

class Link extends BaseModel
{
    public $id; 	
    public $parent_id; 	
    public $name; 	
    public $title; 	
    public $url; 	
    public $target; 	
    public $rel; 	
    public $ordering; 	
    public $group_id; 

    /**
     * @author Vu.Tran
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('link'); 
        
        $this->belongsTo('group_id', 'ITECH\Datasource\Model\LinkGroup', 'id', array(
            'alias' => 'LinkGroup', 
            'reusable' => true  
        ));
    }    
}