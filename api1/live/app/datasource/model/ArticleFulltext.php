<?php
namespace ITECH\Datasource\Model;

use ITECH\Datasource\Model\BaseModel;

class ArticleFulltext extends BaseModel
{ 
    public $article_id; 	
    public $title; 	
    public $content;

    /**
     * @author Vu.Tran
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('article_fulltext');
    }
}