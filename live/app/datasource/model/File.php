<?php
namespace ITECH\Datasource\Model;

use ITECH\Datasource\Model\BaseModel;

class File extends BaseModel
{
    public $id;
    public $category_id;
    public $title; 		
    public $file_name; 	
    public $file_type; 
    public $file_size;
    public $created_at; 	
    public $updated_at; 	
    public $created_by; 	
    public $updated_by; 	
    public $created_ip;

    /**
     * @author Vu.Tran
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('file');
    }
}