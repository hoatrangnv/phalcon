<?php
namespace ITECH\Datasource\Model;

use Phalcon\Mvc\Model\Validator\Uniqueness;
use ITECH\Datasource\Model\BaseModel;

class Banner extends BaseModel
{
    public $id;	
    public $name; 	
    public $image; 	
    public $url; 	
    public $expired_at;
    public $click;
    public $ordering; 	
    public $banner_zone_id; 	
    public $status;

    /**
     * @author Vu.Tran
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('banner');
    }
}