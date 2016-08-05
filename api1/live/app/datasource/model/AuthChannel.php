<?php
namespace ITECH\Datasource\Model;

use Phalcon\Mvc\Model\Validator\Uniqueness;
use ITECH\Datasource\Model\BaseModel;

class AuthChannel extends BaseModel
{
    public $id;
    public $name;
    public $key;

    /**
     * @author Cuong.Bui
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('auth_channel');
    }

    /**
     * @author Cuong.Bui
     */
    public function validation()
    {
        $this->validate(new Uniqueness(array(
            'field' => 'key',
            'message' => 'Key này đã được sử dụng.'
        )));

        return $this->validationHasFailed() ? false : true;
    }
}