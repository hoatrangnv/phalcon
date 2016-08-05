<?php
namespace ITECH\Datasource\Model;

use Phalcon\Mvc\Model\Validator\Uniqueness;
use ITECH\Datasource\Model\BaseModel;

class User extends BaseModel
{
    public $id;
    public $email;
    public $password;
    public $name;
    public $slug;
    public $gender;
    public $birthday;
    public $address;
    public $province;
    public $phone;
    public $mobile;
    public $avatar;
    public $status;
    public $token;
    public $created_at;
    public $updated_at;
    public $logined_at;
    public $logined_ip;
    public $is_premium;
    public $premium_created_at;
    public $premium_renewed_at;
    public $premium_expired_at;
    public $admin_viewed_at;
    public $admin_username;
    public $is_mail_birthday_received;
    public $is_mail_notify_received;
    public $cover_status;
    public $cover_token;
    public $cover_avatar;
    public $cover_image;
    public $cover_expired_at; 

    /**
     * @author Vu.Tran
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('user');
    }

    /**
     * @author Vu.Tran
     */
    public function validation()
    {
        $this->validate(new Uniqueness(array(
            'field' => 'email', 
            'message' => 'Email này đã được sử dụng.'
        ))); 
        return $this->validationHasFailed() ? false : true;
    }
}