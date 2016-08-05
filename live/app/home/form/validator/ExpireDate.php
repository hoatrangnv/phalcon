<?php
namespace ITECH\Home\Form\Validator;

use Phalcon\Validation\Validator;
use Phalcon\Validation\ValidatorInterface;
use Phalcon\Validation\Message;

class ExpireDate extends Validator implements ValidatorInterface
{
    /**
     * @author Cuong.Bui
     */
    public function validate($validator, $attribute)
    {
        $value = $validator->getValue($attribute);
        $message = $this->getOption('message');

        if (strtotime($value) < strtotime('-3 days')) {
            $validator->appendMessage(new Message($message, $attribute, 'expired_at'));
            return false;
        }
    }
}