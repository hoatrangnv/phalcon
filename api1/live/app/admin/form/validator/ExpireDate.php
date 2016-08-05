<?php
namespace ITECH\Admin\Form\Validator;

use Phalcon\Validation;
use Phalcon\Validation\Validator;
use Phalcon\Validation\ValidatorInterface;
use Phalcon\Validation\Message;

class ExpireDate extends Validator implements ValidatorInterface
{
    
    public function validate(\Phalcon\Validation $validator, $attribute)
    {
        $value = $validator->getValue($attribute);
        $message = $this->getOption('message');

        if (strtotime($value) < time()) {
            $validator->appendMessage(new Message($message, $attribute, 'expired_at'));
            return false;
        }
    }
}