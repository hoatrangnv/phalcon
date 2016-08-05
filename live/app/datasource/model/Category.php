<?php
namespace ITECH\Datasource\Model;

use ITECH\Datasource\Model\BaseModel;

class Category extends BaseModel
{
    public $id; 
    public $parent_id; 	
    public $name; 	
    public $slug; 	
    public $title_box;
    public $description; 	
    public $ordering; 	
    public $hits; 	
    public $article_count; 	
    public $image; 
    public $status;   
    public $created_at; 	
    public $module;

    /**
     * @author Vu.Tran
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('category'); 
    }    
}