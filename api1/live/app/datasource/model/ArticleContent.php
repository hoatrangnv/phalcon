<?php
namespace ITECH\Datasource\Model;

use ITECH\Datasource\Model\BaseModel;

class ArticleContent extends BaseModel
{ 
    public $article_id; 	
    public $content; 	
    public $meta_title; 	
    public $meta_description; 	
    public $meta_keyword;
    public $title_content_h2;
    public $title_content_h3;
    public $content2_h2;
    public $content2_h3;

    /**
     * @author Vu.Tran
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('article_content');
    }
}