<?php
namespace ITECH\Auth\Form\Validator;

use Phalcon\Validation\Validator;
use Phalcon\Validation\ValidatorInterface;
use Phalcon\Validation\Message;
use ITECH\Datasource\Lib\Util;

class AdminUsername extends \Phalcon\Validation\Validator implements \Phalcon\Validation\ValidatorInterface
{
    /**
     * @author Cuong.Bui
     */
    public function validate(\Phalcon\Validation $validator, $attribute)
    {
        $value = $validator->getValue($attribute);
        $message = $this->getOption('message');

        if (!Util::usernameValidation($value)) {
            $validator->appendMessage(new \Phalcon\Validation\Message($message, $attribute, 'username'));
            return false;
        }
    }
}