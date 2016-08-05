<?php
namespace ITECH\Datasource\Model;

use ITECH\Datasource\Model\BaseModel;

class ArticleAttribute extends BaseModel
{ 
    public $article_id;
    public $attribute_id;
    public $attribute_value;

    /**
     * @author Vu.Tran
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('article_attribute');
    }
}