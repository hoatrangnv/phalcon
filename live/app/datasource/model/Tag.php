<?php
namespace ITECH\Datasource\Model;

use Phalcon\Mvc\Model\Validator\Uniqueness;
use ITECH\Datasource\Model\BaseModel;

class Tag extends BaseModel
{
    public $id;	
    public $title; 	
    public $slug; 	
    public $meta_title; 	
    public $meta_description; 	
    public $meta_keyword; 	
    public $created_at; 	
    public $updated_at; 	
    public $hits; 	
    public $article_count; 	
    public $product_count;

    /**
     * @author Cuong.Bui
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('tag');
    }

    /**
     * @author Cuong.Bui
     */
    public function validation()
    {
        $this->validate(new Uniqueness(array(
            'field' => 'slug', 
            'message' => 'Tag này đã được sử dụng.'
        ))); 

        return $this->validationHasFailed() ? false : true;
    }
}