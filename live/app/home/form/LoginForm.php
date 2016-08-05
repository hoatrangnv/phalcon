<?php
namespace ITECH\Home\Form;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Password;
use Phalcon\Validation\Validator\Regex;
use Phalcon\Validation\Validator\PresenceOf;

class LoginForm extends Form
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
        
        $password = new Password('password');
        $password->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập password'
            ))
        ));
        $password->setFilters(array('striptags', 'trim'));
        $this->add($password);
    }
}