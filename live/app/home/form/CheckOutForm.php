<?php
namespace ITECH\Home\Form;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Password;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;

class CheckOutForm extends Form
{
    /**
     * @author Vu.Tran
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
        $password->setFilters(array('striptags', 'trim'));
        $this->add($password);
    }
}