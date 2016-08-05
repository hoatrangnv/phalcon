<?php
namespace ITECH\Admin\Form\Validator;

use Phalcon\Validation\Validator\Email;

class ContactEmail extends Email
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