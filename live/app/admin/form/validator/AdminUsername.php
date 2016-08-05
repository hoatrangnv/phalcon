<?php
namespace ITECH\Admin\Form\Validator;

use Phalcon\Validation;
use Phalcon\Validation\Message;
use Phalcon\Validation\Validator;
use Phalcon\Validation\ValidatorInterface;
use ITECH\Datasource\Lib\Util;

class AdminUsername extends Validator implements ValidatorInterface
{
    /**
     * @author phison
     */
    public function validate(Validation $validator, $attribute)
    {
        if ($attribute != '') {
            $value = $validator->getValue($attribute);
        }

        $message = $this->getOption('message');

        if (!Util::usernameValidation($value)) {
            $validator->appendMessage(new Message($message.':'.$value, $attribute, 'username'));
            return false;
        }
    }
}