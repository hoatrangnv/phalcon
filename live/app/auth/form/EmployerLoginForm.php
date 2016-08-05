<?php
namespace ITECH\Auth\Form;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Password;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;

class EmployerLoginForm extends Form
{
    /**
     * @author Cuong.Bui
     */
    public function initialize($model, $options)
    {
        $email = new Text('email');
        $email->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập email.'
            )),
            new Email(array(
                'message' => 'Email không hợp lệ.'
            ))
        ));
        $email->setFilters(array('striptags', 'trim', 'lower'));
        $this->add($email);

        $password = new Password('password');
        $password->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập mật khẩu.'
            ))
        ));
        $this->add($password);
    }
}