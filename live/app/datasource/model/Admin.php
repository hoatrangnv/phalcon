<?php
namespace ITECH\Datasource\Model;

use Phalcon\Mvc\Model\Validator\Uniqueness;
use ITECH\Datasource\Model\BaseModel;

class Admin extends BaseModel
{
    public $id;
    public $username;
    public $password;
    public $name;
    public $email;
    public $phone;
    public $type;
    public $total_article;
    public $total_product;
    public $created_at;
    public $updated_at;
    public $logined_at;
    public $logined_ip;

    /**
     * @author Cuong.Bui
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('admin');
    }

    /**
     * @author Cuong.Bui
     */
    public function validation()
    {
        $this->validate(new Uniqueness(array(
            'field' => 'username',
            'message' => 'Tài khoản này đã được sử dụng.'
        )));

        return $this->validationHasFailed() ? false : true;
    }
}