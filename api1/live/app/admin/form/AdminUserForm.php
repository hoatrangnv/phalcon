<?php
namespace ITECH\Admin\Form;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Select;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Email;
use ITECH\Admin\Form\Validator\AdminUsername;
use ITECH\Datasource\Lib\Constant;

class AdminUserForm extends Form
{
    /**
     * @author Cuong.Bui
     */
    public function initialize($model, $options)
    {
        $username = new Text('username');
        $username->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập tên đăng nhập.'
            )),
            new StringLength(array(
                'min' => 5,
                'messageMinimum' => 'Tên đăng nhập phải lớn hơn hoặc bằng 5 ký tự.'
            )),
            new AdminUsername(array(
                'message' => 'Tên đăng nhập không hợp lệ.'
            ))
        ));
        $username->setFilters(array('striptags', 'lower', 'trim'));
        $this->add($username);

        if (!isset($options['edit'])) {
            $password = new Text('password');
            $password->addValidators(array(
                new PresenceOf(array(
                    'message' => 'Yêu cầu nhập mật khẩu.'
                ))
            ));
            $this->add($password);
        }

        $new_password = new Text('new_password');
        $this->add($new_password);

        $name = new Text('name');
        $name->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập họ tên.'
            )),
            new StringLength(array(
                'min' => 5,
                'messageMinimum' => 'Họ tên phải lớn hơn hoặc bằng 5 ký tự.'
            ))
        ));
        $name->setFilters(array('striptags', 'trim'));
        $this->add($name);

        $email = new Text('email');
        $email->addValidators(array(
            new PresenceOf(array(
                'message' => 'Yêu cầu nhập email.'
            )),
            new Email(array(
                'message' => 'Email không hợp lệ.'
            ))
        ));
        $email->setFilters(array('striptags', 'lower', 'trim'));
        $this->add($email);

        $phone = new Text('phone');
        $phone->setFilters(array('striptags', 'trim'));
        $this->add($phone);

        $type = new Select('type', Constant::adminTypeSelect());
        $this->add($type);
    }
}