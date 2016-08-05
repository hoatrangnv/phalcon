<?php
namespace ITECH\Datasource\Model;

use ITECH\Datasource\Model\BaseModel;

class Config extends BaseModel
{
    /**
     * @author Vu.Tran
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('config');
    }
}