<?php
namespace ITECH\Admin\Form\Validator;

use Phalcon\Validation\Validator;
use Phalcon\Validation\ValidatorInterface;
use Phalcon\Validation\Message;

class Url extends Validator implements ValidatorInterface
{
    /**
     * @author Vu.Tran
     */
    public function validate($validator, $attribute)
    {
        $value = $validator->getValue($attribute);
        $message = $this->getOption('message');

        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            $validator->appendMessage(new Message($message, $attribute, 'url'));
            return false;
        }
    }
}