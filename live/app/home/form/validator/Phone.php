<?php
namespace ITECH\Home\Form\Validator;

use Phalcon\Validation\Validator;
use Phalcon\Validation\ValidatorInterface;
use Phalcon\Validation\Message;
use ITECH\Datasource\Lib\Util;

class Phone extends Validator implements ValidatorInterface
{
    /**
     * @author Cuong.Bui
     */
    public function validate($validator, $attribute)
    {
        $value = $validator->getValue($attribute);
        $message = $this->getOption('message');

        if (is_null($value) || $value == '') {
            return true;
        } else {
            if (!Util::phoneValidation($value)) {
                $validator->appendMessage(new Message($message, $attribute, 'phone'));
                return false;
            }
        }
    }
}