<?php
namespace ITECH\Admin\Form;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Password;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Confirmation;

class AdminChangePasswordForm extends Form
{
    public function initialize($model, $options)
    {
        $old_password = new Password('old_password');
        $old_password->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập mật khẩu cũ.'
            ))
        ));
        $this->add($old_password);

        $new_password = new Password('new_password');
        $new_password->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập mật khẩu mới.'
            ))
        ));
        $this->add($new_password);

        $confirm_password = new Password('confirm_password');
        $confirm_password->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập lại mật khẩu mới.'
            )),
            new Confirmation(array(
                'with' => 'new_password',
                'message' => 'Mật khẩu nhập lại không chính xác.'
            ))
        ));
        $this->add($confirm_password);
    }
}