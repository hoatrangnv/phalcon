<?php
namespace ITECH\Datasource\Model;

use ITECH\Datasource\Model\BaseModel;

class ArticleTag extends BaseModel
{ 
    public $article_id; 	
    public $tag_id;

    /**
     * @author Vu.Tran
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('article_tag');
        
        $this->hasOne('tag_id', 'ITECH\Datasource\Model\Tag', 'id', array(
            'alias' => 'Tag', 
            'reusable' => true  
        ));
    }
}