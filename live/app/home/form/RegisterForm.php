<?php
namespace ITECH\Home\Form;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Password;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\Regex;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Confirmation;

class RegisterForm extends Form
{
    /**
     * @author Vu.Tran
     */
    public function initialize($model, $options)
    {   
        $phone = new Text('phone');
        $phone->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập số điện thoại.'
            )),
            new Regex(array(
                'message' => 'Yêu cầu nhập số.',
                'pattern' => '/^[[:alnum:]]+$/'    
            ))
        ));
        $phone->setFilters(array('striptags', 'trim', 'lower'));
        $this->add($phone);
        
        $name = new Text('name');
        $name->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập họ tên.'
            )),
            new StringLength(array(
                'min' => 5,
                'messageMinimum' => 'Họ tên phải lớn hơn hoặc bằng 5 ký tự.'
            ))
        ));
        $name->setFilters(array('striptags', 'trim'));
        $this->add($name);

        $email = new Text('email');
        $email->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập email.'
            )),
            new Email(array(
                'message' => 'Email không hợp lệ.'
            ))
        ));
        $email->setFilters(array('striptags', 'lower', 'trim'));
        $this->add($email);

        $password = new Password('password');
        $password->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập mật khẩu.'
            ))
        ));
        $this->add($password);

        $confirm_password = new Password('confirm_password');
        $confirm_password->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu xác nhận lại mật khẩu.'
            )),
            new Confirmation(array(
                'message' => 'Mật khẩu nhập lại chưa chính xác.',
                'with' => 'password'
            ))
        ));
        $this->add($confirm_password);
    }
}