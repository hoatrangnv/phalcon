<?php
namespace TVN\Datasource\Model;

use TVN\Datasource\Model\BaseModel;

class Contact extends BaseModel
{
    public $id;
    public $name;
    public $email;
    public $phone;
    public $title;
    public $description;
    public $created_at;
    public $created_ip;

    /**
     * @author Cuong.Bui
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('contact');
    }
}