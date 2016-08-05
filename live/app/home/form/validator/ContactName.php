<?php
namespace ITECH\Home\Form\Validator;

use Phalcon\Validation\Validator\StringLength;

class ContactName extends StringLength
{
    /**
     * @author Cuong.Bui
     */
    public function validate($validator, $attribute)
    {
        $value = $validator->getValue($attribute);

        if (is_null($value) || $value == '') {
            return true;
        }

        return parent::validate($validator, $attribute);
    }
}