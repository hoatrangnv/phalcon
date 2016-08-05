<?php
namespace ITECH\Datasource\Model;

use ITECH\Datasource\Model\BaseModel;

class BannerZone extends BaseModel
{
    public $id; 
    public $name; 	
    public $slug;
    public $width; 
    public $height;
    public $ordering; 

    /**
     * @author Vu.Tran
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('banner_zone'); 
    }    
}