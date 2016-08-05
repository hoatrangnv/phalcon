<?php
namespace ITECH\Datasource\Model;

use ITECH\Datasource\Model\BaseModel;

class Location extends BaseModel
{
    public $id;
    public $title;
    public $parent;
    public $fee;
    public $created_at;
    public $created_by;
    public $created_ip;
    public $updated_at;
    public $updated_by;
    public $updated_ip;
    public $ordering;
    public $is_published;

    /**
     * @author Vu.Tran
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('location');
    }
}