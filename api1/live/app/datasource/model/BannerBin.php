<?php
namespace ITECH\Datasource\Model;

use ITECH\Datasource\Model\BaseModel;

class BannerBin extends BaseModel
{
    public $banner_id; 
    public $category_id; 	

    /**
     * @author Vu.Tran
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('banner_bin'); 
    }    
}