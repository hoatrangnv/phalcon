<?php
namespace ITECH\Auth\Form\Validator;

use Phalcon\Validation\Validator;
use Phalcon\Validation\ValidatorInterface;
use Phalcon\Validation\Message;
use ITECH\Datasource\Lib\Util;

class AdminUsername extends Validator implements ValidatorInterface
{
    /**
     * @author Cuong.Bui
     */
    public function validate($validator, $attribute)
    {
        $value = $validator->getValue($attribute);
        $message = $this->getOption('message');

        if (!Util::usernameValidation($value)) {
            $validator->appendMessage(new Message($message, $attribute, 'username'));
            return false;
        }
    }
}