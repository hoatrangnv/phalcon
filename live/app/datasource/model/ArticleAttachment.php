<?php
namespace ITECH\Datasource\Model;

use ITECH\Datasource\Model\BaseModel;

class ArticleAttachment extends BaseModel
{
    public $id;
    public $article_id;	 
    public $title; 	
    public $alternative; 	
    public $description; 	
    public $file_name; 	
    public $file_type; 
    public $file_size;
    public $created_at; 	
    public $updated_at; 	
    public $created_by; 	
    public $updated_by; 	
    public $created_ip; 
    public $ordering;

    /**
     * @author Vu.Tran
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('article_attachment');
        
        $this->hasOne('article_id', 'ITECH\Datasource\Model\Article', 'id', array(
            'alias' => 'Article', 
            'reusable' => true  
        ));
    }
}