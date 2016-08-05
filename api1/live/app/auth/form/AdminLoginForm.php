<?php
namespace ITECH\Auth\Form;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Password;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use ITECH\Auth\Form\Validator\AdminUsername;

class AdminLoginForm extends Form
{
    /**
     * @author Cuong.Bui
     */
    public function initialize($model, $options)
    {
        $username = new Text('username');
        $username->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập tên đăng nhập.'
            )),
            new StringLength(array(
                'min' => 5,
                'messageMinimum' => 'Tên đăng nhập phải lớn hơn hoặc bằng 5 ký tự.'
            )),
            new AdminUsername(array(
                'message' => 'Tên đăng nhập không hợp lệ.'
            ))
        ));
        $username->setFilters(array('striptags', 'trim'));
        $this->add($username);

        $password = new Password('password');
        $password->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập mật khẩu.'
            ))
        ));
        $this->add($password);
    }
}