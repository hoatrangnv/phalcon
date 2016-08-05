<?php
namespace ITECH\Datasource\Model;

use ITECH\Datasource\Model\BaseModel;

class Article extends BaseModel
{
    public $id;	 
    public $title; 	
    public $alias; 	
    public $intro; 	
    public $image; 	
    public $hits; 	
    public $created_at; 	
    public $updated_at; 	
    public $type; 	
    public $show_comment; 	
    public $comment_count; 	
    public $status; 
    public $ordering;	
    public $created_by; 	
    public $updated_by; 	
    public $created_ip; 	
    public $module;

    
    public function initialize()
    {
        parent::initialize();
        $this->setSource('article');
        
        $this->hasMany('id', 'ITECH\Datasource\Model\ArticleCategory', 'article_id', array(
            'alias' => 'ArticleCategory', 
            'reusable' => true  
        ));
        $this->hasMany('id', 'ITECH\Datasource\Model\ArticleAttachment', 'article_id', array(
            'alias' => 'ArticleAttachment', 
            'reusable' => true  
        ));
        $this->hasMany('id', 'ITECH\Datasource\Model\ArticleTag', 'article_id', array(
            'alias' => 'ArticleTag', 
            'reusable' => true  
        ));
        
        $this->hasMany('id', 'ITECH\Datasource\Model\ArticleAttribute', 'article_id', array(
            'alias' => 'ArticleAttribute', 
            'reusable' => true  
        ));
        
        $this->hasOne('created_by', 'ITECH\Datasource\Model\Admin', 'id', array(
            'alias' => 'Admin', 
            'reusable' => true  
        ));  
        
        $this->hasOne('id', 'ITECH\Datasource\Model\ArticleContent', 'article_id', array(
            'alias' => 'ArticleContent', 
            'reusable' => true  
        ));
        
    }
}