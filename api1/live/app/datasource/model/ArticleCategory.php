<?php
namespace ITECH\Datasource\Model;

use ITECH\Datasource\Model\BaseModel;

class ArticleCategory extends BaseModel
{	 
    public $article_id;
    public $category_id;

    /**
     * @author Vu.Tran
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('article_category');
        
        $this->hasOne('category_id', 'ITECH\Datasource\Model\Category', 'id', array(
            'alias' => 'Category',
            'reusable' => true 
        ));
    }
}