<?php
namespace ITECH\Admin\Form\Validator;

use Phalcon\Validation\Validator;
use Phalcon\Validation\ValidatorInterface;
use Phalcon\Validation\Message;

class HotExpireDate extends Validator implements ValidatorInterface
{
    /**
     * @author Cuong.Bui
     */
    public function validate($validator, $attribute)
    {
        $value = $validator->getValue($attribute);
        $message = $this->getOption('message');

        if ($value) {
            if (strtotime($value) < time()) {
                $validator->appendMessage(new Message($message, $attribute, 'hot_expired_at'));
                return false;
            }
        } else {
            return true;
        }
    }
}