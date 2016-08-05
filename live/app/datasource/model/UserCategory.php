<?php
namespace ITECH\Datasource\Model;

use ITECH\Datasource\Model\BaseModel;

class UserCategory extends BaseModel
{	 
    public $user_id;
    public $category_id;

    public function initialize()
    {
        parent::initialize();
        $this->setSource('user_category');
        
        $this->hasOne('category_id', 'ITECH\Datasource\Model\Category', 'id', array(
            'alias' => 'Category',
            'reusable' => true 
        ));
    }
}